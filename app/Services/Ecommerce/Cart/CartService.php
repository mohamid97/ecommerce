<?php

namespace App\Services\Ecommerce\Cart;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\DTO\Ecommerce\Cart\UpdateCartItemQuantityDTO;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\CartBundelItem;
use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Services\Ecommerce\Cart\Strategies\CartStrategyResolver;
use Illuminate\Support\Collection;

class CartService
{
    public function __construct(
        protected CartRepository     $repo,
        protected CartAction         $action,
        protected CartStrategyResolver $resolver
    ) {}

    /**
     * Add / update a cart item using the appropriate strategy.
     *
     * Strategies resolved from the DTO:
     *   • bundel_id only            → BundleStrategy
     *   • product_id + variant_id   → ProductWithOptionStrategy
     *   • product_id only           → SimpleProductStrategy
     */
    public function StoreToCart(int $userId, AddToCartDTO $dto): mixed
    {
        $strategy = $this->resolver->resolve($dto);

        $strategy->validate($dto);

        return $strategy->store($userId, $dto);
    }

    /**
     * Remove a cart item. Keeps the existing plain logic – no strategy needed here.
     */
    public function RemoveFromCart(int $userId, $dto): void
    {
        if(isset($dto->cartItemId)){
            $this->action->checkCartItemExists($userId ,  $dto->cartItemId);
            $this->repo->removeFromCartWiteItem($dto->cartItemId);


        }else{
            if (isset($dto->bundelId)) {
                $this->action->checkBundelExists($dto->bundelId);
            } else {
                    $this->action->checkProductExists($dto->productId);

                    if (isset($dto->variantId)) {
                        $this->action->checkProductHasOption();
                        $this->action->checkVariantExists($dto->variantId);
                    }
            }
            $this->repo->removeFromCart($userId, $dto);

        } 

       
    }

    public function updateCartItemQuantity(int $userId, UpdateCartItemQuantityDTO $dto): Cart
    {
        $cartItem = $this->action->getUserCartItem($userId, $dto->cartItemId);

        if (!empty($cartItem->bundel_id)) {
            $this->action->checkBundelExists($cartItem->bundel_id);

            foreach ($cartItem->cartBundelItems as $bundleItem) {
                $this->action->checkProductExists($bundleItem->product_id);
                $this->action->checkProductBelongsToBundle($bundleItem->product_id);

                $perBundleQty = $this->action->bundelDetail->quantity ?? 1;
                $totalRequestedQty = $dto->quantity * $perBundleQty;

                if (!empty($bundleItem->variant_id)) {
                    $this->action->checkVariantBelongsToBundle($bundleItem->variant_id);
                    $this->action->checkStockWithOption($totalRequestedQty);
                } else {
                    $this->action->checkStock($totalRequestedQty);
                }
            }
        } else {
            $this->action->checkProductExists($cartItem->product_id);

            if (!empty($cartItem->variant_id)) {
                $this->action->checkProductHasOption();
                $this->action->checkVariantExists($cartItem->variant_id);
                $this->action->checkStockWithOption($dto->quantity);
            } else {
                $this->action->checkStock($dto->quantity);
            }
        }

        return $this->repo->updateCartItemQuantity($cartItem, $dto->quantity);
    }

    public function mapGuestCartData(array $data): Cart
    {
        $items = collect();

        foreach ($data['products'] ?? [] as $productData) {
            $dto = AddToCartDTO::fromRequest($productData);
            $this->validateGuestItem($dto);
            $items->push($this->makeGuestProductItem($dto));
        }

        foreach ($data['bundles'] ?? [] as $bundleData) {
            $dto = AddToCartDTO::fromRequest([
                'bundel_id' => $bundleData['bundle_id'],
                'quantity' => $bundleData['quantity'],
                'bundle_items' => $bundleData['bundle_items'] ?? [],
            ]);

            $this->validateGuestItem($dto);
            $items->push($this->makeGuestBundleItem($dto));
        }

        $cart = new Cart([
            'user_id' => null,
            'status' => null,
        ]);
        $cart->id = null;
        $cart->created_at = now();
        $cart->updated_at = now();
        $cart->setRelation('items', $items);

        return $cart;
    }

    private function validateGuestItem(AddToCartDTO $dto): void
    {
        $strategy = $this->resolver->resolve($dto);
        $strategy->validate($dto);
    }

    private function makeGuestProductItem(AddToCartDTO $dto): CartItem
    {
        $product = Product::with('variants.variants.optionValue.option')->findOrFail($dto->product_id);
        $variant = $dto->variant_id
            ? ProductVariant::with('variants.optionValue.option')->findOrFail($dto->variant_id)
            : null;

        $item = new CartItem([
            'cart_id' => null,
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'bundel_id' => null,
            'type' => 'product',
            'quantity' => $dto->quantity,
            'total_before_discount' => ($variant?->sale_price ?? $product->sale_price) * $dto->quantity,
            'total_after_discount' => ($variant?->getDiscountPrice() ?? $product->getDiscountPrice()) * $dto->quantity,
        ]);

        $item->id = null;
        $item->created_at = now();
        $item->updated_at = now();
        $item->setRelation('product', $product);
        $item->setRelation('variant', $variant);
        $item->setRelation('cartBundelItems', collect());

        return $item;
    }

    private function makeGuestBundleItem(AddToCartDTO $dto): CartItem
    {
        $bundle = Bundel::with('bundelDetails')->findOrFail($dto->bundel_id);
        $priceData = $this->action->getBundlePriceWithData($dto);

        $item = new CartItem([
            'cart_id' => null,
            'product_id' => null,
            'variant_id' => null,
            'bundel_id' => $bundle->id,
            'type' => 'bundel',
            'quantity' => $dto->quantity,
            'total_before_discount' => $priceData['total_price'] * $dto->quantity,
            'total_after_discount' => $priceData['total_discount_price'] * $dto->quantity,
        ]);

        $item->id = null;
        $item->created_at = now();
        $item->updated_at = now();
        $item->setRelation('bundel', $bundle);
        $item->setRelation('cartBundelItems', $this->makeGuestBundleItems($dto->bundle_items));

        return $item;
    }

    private function makeGuestBundleItems(array $bundleItems): Collection
    {
        return collect($bundleItems)->map(function (array $bundleItemData) {
            $product = Product::findOrFail($bundleItemData['product_id']);
            $variant = !empty($bundleItemData['variant_id'])
                ? ProductVariant::with('variants.optionValue.option')->findOrFail($bundleItemData['variant_id'])
                : null;

            $bundleItem = new CartBundelItem([
                'cart_item_id' => null,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
            ]);

            $bundleItem->id = null;
            $bundleItem->created_at = now();
            $bundleItem->updated_at = now();
            $bundleItem->setRelation('product', $product);
            $bundleItem->setRelation('variant', $variant);

            return $bundleItem;
        })->values();
    }
}
