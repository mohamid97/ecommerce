<?php

namespace App\Services\Ecommerce\Cart;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\BundelDetails;
use App\Models\Api\Ecommerce\ProductVariant;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartAction
{
    public ?Product        $product       = null;
    public ?Bundel         $bundel        = null;
    public ?BundelDetails  $bundelDetail  = null;
    public ?ProductVariant $variant       = null;

    // ── Existence checks ────────────────────────────────────────────────────

    public function checkProductExists(int $productId): Product
    {
        $this->product = Product::active()->find($productId);

        if (!$this->product) {
            throw new ModelNotFoundException(
                __('main.model_not_founded', ['model' => 'Product'])
            );
        }

        return $this->product;
    }

    public function checkBundelExists(int $bundelId): Bundel
    {
        $this->bundel = Bundel::with('bundelDetails.product.variants')
            ->where('status', 'active')
            ->find($bundelId);

        if (!$this->bundel || !$this->bundel->hasOnlyActiveItems()) {
            throw new ModelNotFoundException(
                __('main.model_not_founded', ['model' => 'Bundle'])
            );
        }

        return $this->bundel;
    }

    public function checkVariantExists(int $variantId): void
    {
        $this->variant = ProductVariant::where('status', 'active')->find($variantId);

        if (!$this->variant) {
            throw new ModelNotFoundException(
                __('main.model_not_founded', ['model' => 'Product Variant'])
            );
        }
    }

    // ── Bundle-specific checks ───────────────────────────────────────────────

    /**
     * Verify that the given product is listed inside this bundle's details.
     * Stores the matching BundelDetails row for further variant checks.
     */
    public function checkProductBelongsToBundle(int $productId, ?int $variantId = null, ?int $bundleItemId = null): void
    {
        $this->bundelDetail = $this->findBundleDetailForSelection($productId, $variantId, $bundleItemId);

        if (!$this->bundelDetail) {
            throw new ModelNotFoundException(
                __('main.model_not_founded', ['model' => 'Product in Bundle'])
            );
        }
    }

    public function findBundleDetailForSelection(int $productId, ?int $variantId = null, ?int $bundleItemId = null): ?BundelDetails
    {
        if (!$this->bundel) {
            return null;
        }

        return $this->bundel->bundelDetails->first(function (BundelDetails $detail) use ($productId, $variantId, $bundleItemId): bool {
            if ($bundleItemId !== null && (int) $detail->getKey() !== $bundleItemId) {
                return false;
            }

            if ((int) $detail->product_id !== $productId) {
                return false;
            }

            if ($variantId === null) {
                return true;
            }

            $allowedVariantIds = $detail->selectedVariantIds();

            return in_array((string) $variantId, $allowedVariantIds, true);
        });
    }

    /**
     * Verify that the variant is among the allowed variant_ids for this bundle detail.
     */
    public function checkVariantBelongsToBundle(int $variantId): void
    {
        $allowedVariants = $this->bundelDetail?->selectedVariantIds() ?? [];

        if (!in_array((string) $variantId, $allowedVariants, true)) {
            throw new \Exception(
                __('main.model_not_founded', ['model' => 'Variant in Bundle'])
            );
        }

        // Also confirm the variant record exists
        $this->checkVariantExists($variantId);
    }

    // ── Options / variant checks ─────────────────────────────────────────────

    /**
     * Throws if the product does NOT support options/variants.
     */
    public function checkProductHasOption(): void
    {
        if (!$this->product->has_options) {
            throw new \Exception(__('main.product_has_no_options'));
        }
    }

    // ── Stock checks ─────────────────────────────────────────────────────────

    public function checkStockWithOption(int $quantity): void
    {
        $productVariant = ProductVariant::where('product_id', $this->product->id)
            ->where('id', $this->variant->id)
            ->first();

        if (!$productVariant || $productVariant->stock < $quantity) {
            throw new \Exception(__('main.insufficient_stock_for_selected_variant'));
        }
    }

    public function checkStock(int $quantity): void
    {
        if ($this->product->stock < $quantity) {
            throw new \Exception(__('main.insufficient_stock'));
        }
    }

    public function getUserCartItem(int $userId, int $cartItemId): CartItem
    {
        $cartItem = CartItem::with(['cartBundelItems'])
            ->where('id', $cartItemId)
            ->whereHas('cart', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();

        if (!$cartItem) {
            throw new ModelNotFoundException(
                __('main.model_not_founded', ['model' => 'Cart Item'])
            );
        }

        return $cartItem;
    }




    // get bundle price data
    public function getBundlePriceWithData($dto): array
    {
        $bundle = Bundel::with('bundelDetails')->find($dto->bundel_id);
      
        if (!$bundle) {
            return [
                'total_price'          => 0.0,
                'total_discount_price' => 0.0,
            ];
        }

        $totalPrice         = 0.0;
        $totalDiscountPrice = 0.0;
       
        foreach ($dto->bundle_items as $item) {
            $productId = $item['product_id'];
            $variantId = $item['variant_id'] ?? null;
            $bundleItemId = $item['bundle_item_id'] ?? null;

            $bundleDetail = $this->findBundleDetailForSelection($productId, $variantId, $bundleItemId);
            dd('hh' , $bundleDetail);
            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                $price = $variant?->sale_price ?? 0;
                $discountPrice = $variant?->getDiscountPrice() ?? 0;
            } else {
                $product = Product::find($productId);
                $price = $product?->sale_price ?? 0;
                $discountPrice = $product?->getDiscountPrice() ?? 0;
            }

            if (!$bundleDetail) {
                continue; // skip if this product isn't part of the bundle
            }

            $qty                = $bundleDetail->quantity;
            $totalPrice         += $price         * $qty;
            $totalDiscountPrice += $discountPrice * $qty;
        }

        if ($bundle->hasBundleDiscount()) {
            $totalDiscountPrice = $bundle->applyBundleDiscount($totalPrice);
        }

        return [
            'total_price'          => $totalPrice,
            'total_discount_price' => $totalDiscountPrice,
        ];
        
    }


    // check cart item exists
    public function checkCartItemExists($userId ,  $cartItemId): void
    {
        $cartItem = CartItem::with('cart')->find($cartItemId);
        if (!$cartItem) {
            throw new ModelNotFoundException(
                __('main.model_not_founded', ['model' => 'Cart Item'])
            );
        }
        if ($cartItem->cart->user_id !== $userId) {
            throw new ModelNotFoundException(
                __('main.model_not_founded', ['model' => 'Cart Item'])
            );
        }

        


    }



    // ── Variant resolution ───────────────────────────────────────────────────

    /**
     * If the product has options and the DTO has no variant_id, automatically
     * select the default variant (is_default = 1), falling back to the first
     * active variant. Throws if the product has options but zero active variants.
     *
     * Mutates $dto->variant_id in-place.
     */
    public function resolveDefaultVariant(AddToCartDTO $dto): void
    {
        $product = Product::find($dto->product_id);

        if (!$product || !$product->has_options) {
            // Simple product — nothing to resolve
            return;
        }

        $variant = ProductVariant::where('product_id', $dto->product_id)
            ->where('status', 'active')
            ->orderByDesc('is_default') // is_default=1 floats to the top
            ->first();

        if (!$variant) {
            throw new \Exception(__('main.product_has_no_variants'));
        }

        $dto->variant_id = $variant->id;
    }

    /**
     * For every bundle item that carries a product with options but no variant_id,
     * auto-resolve the variant using the same default → first-active logic.
     *
     * Mutates the variant_id entries inside $dto->bundle_items in-place.
     */
    public function resolveBundleItemVariants(AddToCartDTO $dto): void
    {
        $resolvedItems = [];

        foreach ($dto->bundle_items as $item) {
            $productId = $item['product_id'] ?? null;
            $variantId = $item['variant_id'] ?? null;
            $bundleItemId = $item['bundle_item_id'] ?? null;

            if ($productId && !$variantId) {
                $product = Product::find($productId);

                if ($product && $product->has_options) {
                    $allowedVariantIds = [];

                    if ($bundleItemId) {
                        $allowedVariantIds = BundelDetails::where('id', $bundleItemId)
                            ->where('product_id', $productId)
                            ->first()
                            ?->selectedVariantIds() ?? [];
                    }

                    $variantQuery = ProductVariant::where('product_id', $productId)
                        ->where('status', 'active')
                        ->orderByDesc('is_default');

                    if (!empty($allowedVariantIds)) {
                        $variantQuery->whereIn('id', $allowedVariantIds);
                    }

                    $variant = $variantQuery->first();

                    if (!$variant) {
                        throw new \Exception(
                            __('main.product_has_no_variants') . " (Product ID: {$productId})"
                        );
                    }

                    $item['variant_id'] = $variant->id;
                }
            }

            $resolvedItems[] = $item;
        }

        $dto->bundle_items = $resolvedItems;
    }



    // validate MOQ for product or variant
    public function validateMOQ(string $type, int $id, int $quantity): void
    {
        if ($type === 'product') {
            $product = Product::find($id);
            if (!$product) {
                throw new ModelNotFoundException(
                    __('main.model_not_founded', ['model' => 'Product'])
                );
            }
            $moq = $product->moq ?? 1;
        } elseif ($type === 'variant') {
            $variant = ProductVariant::find($id);
            if (!$variant) {
                throw new ModelNotFoundException(
                    __('main.model_not_founded', ['model' => 'Product Variant'])
                );
            }
            $moq = $variant->moq ?? 1;
        } else {
            throw new \InvalidArgumentException('Invalid type for MOQ validation');
        }

        if ($quantity < $moq) {
            throw new \Exception(__('main.minimum_order_quantity_not_met', ['moq' => $moq]));
        }


    }




}
