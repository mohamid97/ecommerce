<?php

namespace App\Services\Ecommerce\Order;

use App\Models\Api\Ecommerce\PointsSetting;
use App\Models\User;
use App\Models\Api\Ecommerce\Order;

class PointsService
{
    /**
     * Apply points for authenticated user to the given order.
     * Returns monetary amount applied from points.
     * Throws Exception on invalid usage.
     */
    public function applyPointsToOrder(User $user, Order $order, int $pointsToUse, float $baseAmount): float
    {
        if ($pointsToUse <= 0) {
            return 0.0;
        }

        $setting = PointsSetting::first();
        if (!$setting) {
            throw new \Exception(__('main.points_not_configured'));
        }

        if ($baseAmount < ($setting->min_order_amount ?? 0)) {
            throw new \Exception(__('main.points_min_order'));
        }

        if (($user->points ?? 0) < $pointsToUse) {
            throw new \Exception(__('main.insufficient_points'));
        }

        $amount = $pointsToUse * ($setting->pound_per_point ?? 0);
        $amount = max(0, (float) $amount);

        // Do not allow points to exceed order amount
        $amount = min($amount, $baseAmount);

        $order->points_used = $pointsToUse;
        $order->points_amount = $amount;

        // Deduct from user immediately (alternatively reserve until capture)
        $user->points = max(0, ($user->points ?? 0) - $pointsToUse);
        $user->save();

        return $amount;
    }

    /**
     * Calculate monetary amount for given points without mutating user or order.
     * Validates rules and returns the monetary amount that would be applied.
     */
    public function calculatePointsAmount(User $user, int $pointsToUse, float $baseAmount): float
    {
        if ($pointsToUse <= 0) {
            return 0.0;
        }

        $setting = PointsSetting::first();
        if (!$setting) {
            throw new \Exception(__('main.points_not_configured'));
        }

        if ($baseAmount < ($setting->min_order_amount ?? 0)) {
            throw new \Exception(__('main.points_min_order'));
        }

        if (($user->points ?? 0) < $pointsToUse) {
            throw new \Exception(__('main.insufficient_points'));
        }

        $amount = $pointsToUse * ($setting->pound_per_point ?? 0);
        $amount = max(0, (float) $amount);

        // Do not allow points to exceed order amount
        $amount = min($amount, $baseAmount);

        return $amount;
    }

    /**
     * Award configured loyalty points once an authenticated order is delivered and paid.
     */
    public function awardPointsForCompletedPaidOrder(Order $order): int
    {
        if (!$order->user_id || $order->status !== 'delivered' || $order->payment_status !== 'paid') {
            return 0;
        }

        if (($order->points_earned ?? 0) > 0) {
            return 0;
        }

        $setting = PointsSetting::first();
        if (!$setting || ($setting->points ?? 0) <= 0) {
            return 0;
        }

        $orderAmount = (float) ($order->total_after_discount ?? $order->total ?? 0);
        if ($orderAmount < (float) ($setting->min_order_amount ?? 0)) {
            return 0;
        }

        $user = $order->user ?: User::find($order->user_id);
        if (!$user) {
            return 0;
        }

        $points = (int) $setting->points;
        $user->points = (int) ($user->points ?? 0) + $points;
        $user->save();

        $order->points_earned = $points;
        $order->save();

        return $points;
    }
}
