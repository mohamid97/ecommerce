<?php

namespace App\Services\Ecommerce\Order;

use App\Models\Api\Ecommerce\Promotion;
use App\Models\Api\Ecommerce\Order;

class CouponService
{
    /**
     * Apply coupon to the given order using the provided coupon code and base amount.
     * Returns the computed discount amount.
     * Throws Exception on invalid coupon or exhausted coupon limit.
     */
    public function applyCouponToOrder(Order $order, string $couponCode, float $baseAmount): float
    {
        $promo = Promotion::where('is_coupon', true)
            ->where('coupon_code', $couponCode)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        if (!$promo) {
            throw new \Exception(__('main.invalid_coupon'));
        }

        if (!is_null($promo->coupon_limit) && $promo->coupon_limit <= 0) {
            throw new \Exception(__('main.coupon_limit_reached'));
        }

        $discount = 0.0;
        if ($promo->type === 'percent') {
            $discount = ($baseAmount * ($promo->discount / 100));
        } elseif ($promo->type === 'fixed') {
            $discount = (float) $promo->discount;
        }

        if (!is_null($promo->max_amount_discount)) {
            $discount = min($discount, (float) $promo->max_amount_discount);
        }

        $order->discount = $discount;
        $order->discount_type = 'coupon';
        $order->coupon_code = $promo->coupon_code ?? $couponCode;

        if (!is_null($promo->coupon_limit)) {
            $promo->coupon_limit = max(0, $promo->coupon_limit - 1);
            $promo->save();
        }

        return $discount;
    }

    /**
     * Calculate coupon discount without mutating promotion or order state.
     * Returns the computed discount amount or throws Exception if invalid.
     */
    public function calculateCouponDiscount(string $couponCode, float $baseAmount): float
    {
        $promo = Promotion::where('is_coupon', true)
            ->where('coupon_code', $couponCode)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        if (!$promo) {
            throw new \Exception(__('main.invalid_coupon'));
        }

        if (!is_null($promo->coupon_limit) && $promo->coupon_limit <= 0) {
            throw new \Exception(__('main.coupon_limit_reached'));
        }

        $discount = 0.0;
        if ($promo->type === 'percent') {
            $discount = ($baseAmount * ($promo->discount / 100));
        } elseif ($promo->type === 'fixed') {
            $discount = (float) $promo->discount;
        }

        if (!is_null($promo->max_amount_discount)) {
            $discount = min($discount, (float) $promo->max_amount_discount);
        }

        return $discount;
    }
}
