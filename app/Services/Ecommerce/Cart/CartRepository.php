<?php

namespace App\Services\Ecommerce\Cart;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\ProductVariant;

class CartRepository
{
    /**
     * Create or update a cart item.
     *
     * Match keys per strategy:
     *   BundleStrategy            -> cart_id + bundel_id
     *   ProductWithOptionStrategy -> cart_id + product_id + variant_id
     *   SimpleProductStrategy     -> cart_id + product_id + variant_id(null) + bundel_id(null)
     */
    public function createOrUpdateCard(int $userId, AddToCartDTO $dto, $priceData = null): Cart
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        if (isset($dto->bundel_id)) {
            $matchKey = [
                'cart_id' => $cart->id,
                'bundel_id' => $dto->bundel_id,
            ];

            $quantity = $this->resolveQuantity($matchKey, $dto->quantity);

            $attributes = [
                'product_id' => null,
                'variant_id' => null,
                'quantity' => $quantity,
                'type' => 'bundel',
                'total_before_discount' => $priceData['total_price'] * $quantity,
                'total_after_discount' => $priceData['total_discount_price'] * $quantity,
            ];

            $cartItem = CartItem::updateOrCreate($matchKey, $attributes);

            $cartItem->cartBundelItems()->delete();

            $bundleItemRows = [];
            foreach ($dto->bundle_items as $item) {
                $bundleItemRows[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                ];
            }

            $cartItem->cartBundelItems()->createMany($bundleItemRows);
        } elseif (isset($dto->variant_id)) {
            $matchKey = [
                'cart_id' => $cart->id,
                'product_id' => $dto->product_id,
                'variant_id' => $dto->variant_id,
            ];

            $quantity = $this->resolveQuantity($matchKey, $dto->quantity);
            $variant = ProductVariant::find($dto->variant_id);

            $attributes = [
                'quantity' => $quantity,
                'bundel_id' => null,
                'type' => 'variant',
                'total_before_discount' => $variant->sale_price * $quantity,
                'total_after_discount' => $variant->getDiscountPrice() * $quantity,
            ];

            CartItem::updateOrCreate($matchKey, $attributes);
        } else {
            $matchKey = [
                'cart_id' => $cart->id,
                'product_id' => $dto->product_id,
                'variant_id' => null,
                'bundel_id' => null,
            ];

            $quantity = $this->resolveQuantity($matchKey, $dto->quantity);
            $product = Product::find($dto->product_id);

            $attributes = [
                'quantity' => $quantity,
                'type' => 'product',
                'total_before_discount' => $product->sale_price * $quantity,
                'total_after_discount' => $product->getDiscountPrice() * $quantity,
            ];

            CartItem::updateOrCreate($matchKey, $attributes);
        }

        return $cart;
    }

    public function removeFromCart(int $userId, $dto): void
    {
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return;
        }

        if (isset($dto->bundelId)) {
            CartItem::where('cart_id', $cart->id)
                ->where('bundel_id', $dto->bundelId)
                ->delete();
        } else {
            CartItem::where('cart_id', $cart->id)
                ->where('product_id', $dto->productId)
                ->when($dto->variantId, fn ($q) => $q->where('variant_id', $dto->variantId))
                ->delete();
        }

        if ($cart->items()->count() === 0) {
            $cart->delete();
        }
    }

    public function updateCartItemQuantity(CartItem $cartItem, int $quantity): Cart
    {
        if (!empty($cartItem->bundel_id)) {
            $bundlePriceData = $this->getBundleTotalsFromCartItem($cartItem);

            $cartItem->update([
                'quantity' => $quantity,
                'total_before_discount' => $bundlePriceData['total_price'] * $quantity,
                'total_after_discount' => $bundlePriceData['total_discount_price'] * $quantity,
            ]);
        } elseif (!empty($cartItem->variant_id)) {
            $variant = ProductVariant::findOrFail($cartItem->variant_id);

            $cartItem->update([
                'quantity' => $quantity,
                'total_before_discount' => $variant->sale_price * $quantity,
                'total_after_discount' => $variant->getDiscountPrice() * $quantity,
            ]);
        } else {
            $product = Product::findOrFail($cartItem->product_id);

            $cartItem->update([
                'quantity' => $quantity,
                'total_before_discount' => $product->sale_price * $quantity,
                'total_after_discount' => $product->getDiscountPrice() * $quantity,
            ]);
        }

        return Cart::with([
            'items.product',
            'items.variant.variants.optionValue.option',
            'items.bundel',
            'items.cartBundelItems.product',
            'items.cartBundelItems.variant.variants.optionValue.option',
        ])->findOrFail($cartItem->cart_id);
    }

    private function resolveQuantity(array $matchKey, int $requestedQuantity): int
    {
        $existingItem = CartItem::where($matchKey)->first();

        return ((int) $existingItem?->quantity) + $requestedQuantity;
    }

    private function getBundleTotalsFromCartItem(CartItem $cartItem): array
    {
        $totalPrice = 0.0;
        $totalDiscountPrice = 0.0;

        $bundle = $cartItem->bundel()->with('bundelDetails')->first();

        foreach ($cartItem->cartBundelItems as $item) {
            $bundleDetail = $bundle?->bundelDetails
                ?->firstWhere('product_id', $item->product_id);

            if (!$bundleDetail) {
                continue;
            }

            if (!empty($item->variant_id)) {
                $variant = ProductVariant::find($item->variant_id);
                $price = $variant?->sale_price ?? 0;
                $discountPrice = $variant?->getDiscountPrice() ?? 0;
            } else {
                $product = Product::find($item->product_id);
                $price = $product?->sale_price ?? 0;
                $discountPrice = $product?->getDiscountPrice() ?? 0;
            }

            $totalPrice += $price * $bundleDetail->quantity;
            $totalDiscountPrice += $discountPrice * $bundleDetail->quantity;
        }

        return [
            'total_price' => $totalPrice,
            'total_discount_price' => $totalDiscountPrice,
        ];
    }



    public function removeFromCartWiteItem(int $cartItemId): void
    {
        $cartItem = CartItem::find($cartItemId);

        if (!$cartItem) {
            return;
        }

        $cartItem->delete();

        $cart = Cart::find($cartItem->cart_id);
        if ($cart && $cart->items()->count() === 0) {
            $cart->delete();
        }
    }
}
