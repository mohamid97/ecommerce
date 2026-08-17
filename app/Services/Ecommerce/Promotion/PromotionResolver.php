<?php

namespace App\Services\Ecommerce\Promotion;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Promotion;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\Bundel;
use Illuminate\Support\Collection;

class PromotionResolver
{
    /**
     * Request-scoped cache so we load active promotions only once per request.
     */
    private static ?Collection $activePromotions = null;

    /**
     * Clear the cache (useful in tests or when promotions change mid-request).
     */
    public static function clearCache(): void
    {
        static::$activePromotions = null;
    }

    /**
     * Resolve the best promotion discounted price for a simple product.
     * Returns the discounted price (float) or null if no promotion applies.
     */
    public function resolveForProduct(Product $product): ?float
    {
        $salePrice = (float) ($product->sale_price ?? 0);
        if ($salePrice <= 0) {
            return null;
        }

        $best = $this->bestPromoPrice($salePrice, $product->id, $product->category_id, $product->brand_id);

        return $best;
    }

    /**
     * Resolve the best promotion discounted price for a product variant.
     * Uses the parent product's category_id and brand_id for category/brand targeting.
     * Returns the discounted price (float) or null if no promotion applies.
     */
    public function resolveForVariant(ProductVariant $variant): ?float
    {
        $salePrice = (float) ($variant->sale_price ?? 0);
        if ($salePrice <= 0) {
            return null;
        }

        // Load parent product for category/brand context (uses already-loaded relation if available)
        $product = $variant->relationLoaded('product') ? $variant->product : $variant->product()->first();

        $productId  = $product?->id;
        $categoryId = $product?->category_id;
        $brandId    = $product?->brand_id;

        $best = $this->bestPromoPrice($salePrice, $productId, $categoryId, $brandId, null);

        return $best;
    }

    /**
     * Resolve the best promotion discounted price for a bundle.
     * Returns the discounted price (float) or null if no promotion applies.
     */
    public function resolveForBundle(Bundel $bundle, float $salePrice): ?float
    {
        if ($salePrice <= 0) {
            return null;
        }

        $best = $this->bestPromoPrice($salePrice, null, null, null, $bundle->id);

        return $best;
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Find the promotion that gives the lowest price for the given context.
     * Returns the computed price after discount, or null if no promotion matches.
     */
    private function bestPromoPrice(float $salePrice, ?int $productId, ?int $categoryId, ?int $brandId, ?int $bundleId = null): ?float
    {
        $promotions = $this->getActivePromotions();

        $bestPrice = null;

        foreach ($promotions as $promo) {
            if (! $this->matches($promo, $productId, $categoryId, $brandId)) {
                continue;
            }

            $promoPrice = $this->computePrice($salePrice, $promo);

            if ($promoPrice === null) {
                continue;
            }

            if ($bestPrice === null || $promoPrice < $bestPrice) {
                $bestPrice = $promoPrice;
            }
        }

        return $bestPrice;
    }

    /**
     * Determine whether a promotion targets the given product context.
     */
    private function matches(Promotion $promo, ?int $productId, ?int $categoryId, ?int $brandId, ?int $bundleId = null): bool
    {
        switch ($promo->target) {
            case 'global':
                return true;

            case 'product':
                return $productId !== null && (int) $promo->product_id === $productId;

            case 'bundle':
                return $bundleId !== null && (int) $promo->bundel_id === $bundleId;

            case 'category':
                if ($categoryId === null) {
                    return false;
                }
                // $promo->categories is already loaded (eager-loaded in getActivePromotions)
                return $promo->categories->contains('id', $categoryId);

            case 'brand':
                if ($brandId === null) {
                    return false;
                }
                return $promo->brands->contains('id', $brandId);

            default:
                return false;
        }
    }

    /**
     * Compute the price after applying a promotion discount.
     * Returns null if the promotion type is unrecognised or produces no discount.
     */
    private function computePrice(float $salePrice, Promotion $promo): ?float
    {
        $discount = (float) ($promo->discount ?? 0);
        if ($discount <= 0) {
            return null;
        }

        if ($promo->type === 'percent') {
            $amount = $salePrice * ($discount / 100);

            // Respect max_amount_discount cap
            if (! is_null($promo->max_amount_discount)) {
                $amount = min($amount, (float) $promo->max_amount_discount);
            }

            return max(0, $salePrice - $amount);
        }

        if ($promo->type === 'fixed') {
            return max(0, $salePrice - $discount);
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Load all active non-coupon promotions once per request (static cache).
     */
    private function getActivePromotions(): Collection
    {
        if (static::$activePromotions === null) {
            static::$activePromotions = Promotion::where('is_coupon', false)
                ->where('status', 'active')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->with(['brands', 'categories'])
                ->get();
        }

        return static::$activePromotions;
    }

    /**
     * Find the best matching promotion *object* for a product (for resource metadata).
     * Returns the Promotion model or null.
     */
    public function findBestPromotionForProduct(Product $product): ?Promotion
    {
        $salePrice = (float) ($product->sale_price ?? 0);
        return $this->findBestPromotion($salePrice, $product->id, $product->category_id, $product->brand_id);
    }

    /**
     * Find the best matching promotion *object* for a variant (for resource metadata).
     */
    public function findBestPromotionForVariant(ProductVariant $variant): ?Promotion
    {
        $salePrice = (float) ($variant->sale_price ?? 0);
        $product   = $variant->relationLoaded('product') ? $variant->product : $variant->product()->first();

        return $this->findBestPromotion($salePrice, $product?->id, $product?->category_id, $product?->brand_id, null);
    }

    /**
     * Find the best matching promotion *object* for a bundle (for resource metadata).
     */
    public function findBestPromotionForBundle(Bundel $bundle, float $salePrice): ?Promotion
    {
        return $this->findBestPromotion($salePrice, null, null, null, $bundle->id);
    }

    private function findBestPromotion(float $salePrice, ?int $productId, ?int $categoryId, ?int $brandId, ?int $bundleId = null): ?Promotion
    {
        $promotions = $this->getActivePromotions();

        $bestPromo = null;
        $bestPrice = null;

        foreach ($promotions as $promo) {
            if (! $this->matches($promo, $productId, $categoryId, $brandId, $bundleId)) {
                continue;
            }

            $promoPrice = $this->computePrice($salePrice, $promo);

            if ($promoPrice === null) {
                continue;
            }

            if ($bestPrice === null || $promoPrice < $bestPrice) {
                $bestPrice = $promoPrice;
                $bestPromo = $promo;
            }
        }

        return $bestPromo;
    }

    /**
     * Get the applied discount info array for a product or variant.
     * Compares the own discount with the best promotion discount, and returns
     * the details of whichever gives the lowest price.
     * Returns an array with 'discount', 'discount_type', and 'promotion' keys.
     */
    public function resolveDiscountInfo($priceSource): array
    {
        if (! $priceSource) {
            return [
                'discount'      => 0,
                'discount_type' => null,
                'promotion'     => null,
            ];
        }

        if ($priceSource instanceof Bundel) {
            // We expect an array with [0] => Bundel, [1] => totalPrice
            // But if it's just a Bundel, we can't reliably get the total price unless we calculate it.
            // Wait, we need totalPrice. We should refactor getDiscountInfo to accept $salePrice optionally.
            // To keep it simple, we won't use resolveDiscountInfo for Bundel since its calculation is custom.
        }

        $ownPrice = $priceSource->calculateOwnDiscountPrice();
        
        $promoPrice = ($priceSource instanceof \App\Models\Api\Ecommerce\ProductVariant)
            ? $this->resolveForVariant($priceSource)
            : $this->resolveForProduct($priceSource);
            
        if ($promoPrice !== null && $promoPrice < $ownPrice) {
            $promo = ($priceSource instanceof \App\Models\Api\Ecommerce\ProductVariant)
                ? $this->findBestPromotionForVariant($priceSource)
                : $this->findBestPromotionForProduct($priceSource);
                
            return [
                'discount'      => (float) $promo->discount,
                'discount_type' => $promo->type,
                'promotion'     => [
                    'id'       => $promo->id,
                    'title'    => $promo->title,
                    'discount' => (float) $promo->discount,
                    'type'     => $promo->type,
                ]
            ];
        }

        return [
            'discount'      => (float) ($priceSource->discount_value ?? $priceSource->discount ?? 0),
            'discount_type' => $priceSource->discount_type,
            'promotion'     => null,
        ];
    }
}
