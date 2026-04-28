<?php

namespace App\Services\Ecommerce\Cart;

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
    public function checkProductBelongsToBundle(int $productId): void
    {
        $this->bundelDetail = BundelDetails::where('bundel_id', $this->bundel->id)
            ->where('product_id', $productId)
            ->first();

        if (!$this->bundelDetail) {
            throw new ModelNotFoundException(
                __('main.model_not_founded', ['model' => 'Product in Bundle'])
            );
        }
    }

    /**
     * Verify that the variant is among the allowed variant_ids for this bundle detail.
     */
    public function checkVariantBelongsToBundle(int $variantId): void
    {
        $allowedVariants = $this->bundelDetail->variant_ids ?? [];

        if (!in_array($variantId, $allowedVariants, true)) {
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

            // Find the matching bundle detail scoped to this bundle
            if ($variantId) {


                $bundleDetail = $bundle->bundelDetails
                    ->where('product_id', $productId)
                    ->first(fn($d) => in_array($variantId, $d->variant_ids ?? []));

                $variant           = ProductVariant::find($variantId);
                $price             = $variant?->sale_price         ?? 0;
                $discountPrice     = $variant?->getDiscountPrice() ?? 0;
            } else {
                $bundleDetail = $bundle->bundelDetails
                    ->where('product_id', $productId)
                    ->first();

                $product           = Product::find($productId);
                $price             = $product?->sale_price         ?? 0;
                $discountPrice     = $product?->getDiscountPrice() ?? 0;
            }

            if (!$bundleDetail) {
                continue; // skip if this product isn't part of the bundle
            }

            $qty                = $bundleDetail->quantity;
            $totalPrice         += $price         * $qty;
            $totalDiscountPrice += $discountPrice * $qty;
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


    
}
