<?php

namespace App\Services\Ecommerce\Product;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\Promotion;
use App\Models\Api\Ecommerce\Bundel;

class PromotionPriceService
{
    /**
     * Return the lowest valid unit price from the product/variant discount and
     * any matching automatic promotion. Coupons are deliberately excluded.
     */
    public function getDiscountPrice(Product $product, ?ProductVariant $variant = null): float
    {
        $product->loadMissing('category', 'brand');

        $basePrice = (float) ($variant?->sale_price ?? $product->sale_price ?? 0);
        $bestPrice = $this->applyOwnDiscount($basePrice, $product->discount, $product->discount_type);

        if ($variant) {
            $bestPrice = min($bestPrice, $this->applyOwnDiscount(
                $basePrice,
                $variant->discount_value,
                $variant->discount_type
            ));
        }

        $promotions = Promotion::query()
            ->where('is_coupon', false)
            ->where('status', 'active')
            ->whereIn('type', ['percent', 'fixed'])
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->where(function ($query) use ($product) {
                $query->where('target', 'global')
                    ->orWhere(function ($query) use ($product) {
                        $query->where('target', 'product')->where('product_id', $product->id);
                    })
                    ->orWhere(function ($query) use ($product) {
                        $query->where('target', 'category')
                            ->whereHas('categories', fn ($categories) => $categories->whereKey($product->category_id));
                    })
                    ->orWhere(function ($query) use ($product) {
                        $query->where('target', 'brand')
                            ->whereHas('brands', fn ($brands) => $brands->whereKey($product->brand_id));
                    });
            })
            ->orderBy('id')
            ->get();

        foreach ($promotions as $promotion) {
            $bestPrice = min($bestPrice, $this->applyPromotionDiscount($basePrice, $promotion));
        }

        return round(max(0, $bestPrice), 2);
    }

    /**
     * Bundle promotions compete with the bundle's own discount and the prices
     * of its selected, already-discounted component items.
     */
    public function getBundleDiscountPrice(Bundel $bundle, float $basePrice, float $componentDiscountPrice): float
    {
        $bestPrice = $componentDiscountPrice;

        if ($bundle->hasBundleDiscount()) {
            $bestPrice = min($bestPrice, $bundle->applyBundleDiscount($basePrice));
        }

        $promotions = Promotion::query()
            ->where('is_coupon', false)
            ->where('status', 'active')
            ->whereIn('type', ['percent', 'fixed'])
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->where(function ($query) use ($bundle) {
                $query->where('target', 'global')
                    ->orWhere(function ($query) use ($bundle) {
                        $query->where('target', 'bundle')->where('bundel_id', $bundle->id);
                    });
            })
            ->orderBy('id')
            ->get();

        foreach ($promotions as $promotion) {
            $bestPrice = min($bestPrice, $this->applyPromotionDiscount($basePrice, $promotion));
        }

        return round(max(0, $bestPrice), 2);
    }

    /**
     * Get the effective discount source for a bundle.
     * Returns ['discount' => float, 'discount_type' => 'percent'|'fixed', 'source' => 'bundle_own'|'promotion'|'global'|'component']
     * representing the discount that produced the final price_after_discount.
     */
    public function getBundleEffectiveDiscount(Bundel $bundle, float $basePrice, float $componentDiscountPrice): array
    {
        $bestPrice = $componentDiscountPrice;
        $bestSource = 'component';
        $bestDiscount = (float) $bundle->discount ?? 0;
        $bestDiscountType = $bundle->discount_type ?? 'fixed';

        // Check bundle's own discount
        if ($bundle->hasBundleDiscount()) {
            $bundleDiscounted = $bundle->applyBundleDiscount($basePrice);
            if ($bundleDiscounted < $bestPrice) {
                $bestPrice = $bundleDiscounted;
                $bestSource = 'bundle_own';
                $bestDiscount = (float) $bundle->discount;
                $bestDiscountType = $bundle->discount_type ?? 'fixed';
            }
        }

        // Check bundle-targeted promotions
        $promotions = Promotion::query()
            ->where('is_coupon', false)
            ->where('status', 'active')
            ->whereIn('type', ['percent', 'fixed'])
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->where(function ($query) use ($bundle) {
                $query->where('target', 'global')
                    ->orWhere(function ($query) use ($bundle) {
                        $query->where('target', 'bundle')->where('bundel_id', $bundle->id);
                    });
            })
            ->orderBy('id')
            ->get();

        foreach ($promotions as $promotion) {
            $discounted = $this->applyPromotionDiscount($basePrice, $promotion);
            if ($discounted < $bestPrice) {
                $bestPrice = $discounted;
                $bestSource = 'promotion';
                $bestDiscount = (float) $promotion->discount;
                $bestDiscountType = $promotion->type ?? 'fixed';
            }
        }

        // Compute effective percentage discount
        $effectivePercent = $basePrice > 0 ? (1 - ($bestPrice / $basePrice)) * 100 : 0;

        return [
            'discount' => round($effectivePercent, 2),
            'discount_type' => 'percent',
            'source' => $bestSource,
        ];
    }

    private function applyOwnDiscount(float $price, mixed $discount, ?string $type): float
    {
        if (!is_numeric($discount) || (float) $discount <= 0) {
            return $price;
        }

        return $this->discountedPrice($price, (float) $discount, $type);
    }

    private function applyPromotionDiscount(float $price, Promotion $promotion): float
    {
        $discount = (float) $promotion->discount;

        if ($promotion->type === 'percent') {
            $discount = $price * ($discount / 100);
            if ($promotion->max_amount_discount !== null) {
                $discount = min($discount, (float) $promotion->max_amount_discount);
            }

            return max(0, $price - $discount);
        }

        return $this->discountedPrice($price, $discount, 'fixed');
    }

    private function discountedPrice(float $price, float $discount, ?string $type): float
    {
        if ($type === 'percentage' || $type === 'percent') {
            return max(0, $price - ($price * ($discount / 100)));
        }

        return max(0, $price - $discount);
    }
}