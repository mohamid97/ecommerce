<?php

namespace App\Services\Ecommerce\Order;

use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\Order;
use Illuminate\Support\Facades\DB;
use App\Services\Ecommerce\Order\CouponService;
use App\Services\Ecommerce\Order\PointsService;

class OrderService
{
    public function __construct(
        protected OrderRepository $repo,
        protected OrderAction $action,
        protected OrderStrategyResolver $resolver,
        protected CouponService $couponService,
        protected PointsService $pointsService
    ) {}
    /**
     * Create order from user's cart with stock validation and allocations.
     * Returns created Order.
     * Throws Exception on insufficient stock.
     */
    public function createOrderFromCart($user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            // Get user's open cart
            $cart = $this->action->getUserOpenCart($user->id);

            // create main order record
            $order = $this->repo->createOrder($user, $data);
            
            $total = 0;
            $totalAfterDiscount = 0;

            foreach ($cart->items as $cartItem) {
               
                $strategy = $this->resolver->resolve($cartItem);
                [$itemTotal, $itemTotalAfterDiscount] = $strategy->handle($cartItem, $order, $this->repo);
                $total += $itemTotal;
                $totalAfterDiscount += $itemTotalAfterDiscount;
            }

            // points handling (optional) for authenticated users
            if (!empty($data['use_points']) && !empty($data['points_to_use'])) {
                $pointsToUse = (int) $data['points_to_use'];
                $pointsAmount = $this->pointsService->applyPointsToOrder($user, $order, $pointsToUse, $totalAfterDiscount);
                // pointsService already sets order->points_used and order->points_amount and deducts user points
            }
           

            // apply coupon (promotion) if provided
            if (!empty($data['coupon_code'])) {
                $discountAmount = $this->couponService->applyCouponToOrder($order, $data['coupon_code'], $totalAfterDiscount);
                $totalAfterDiscount = max(0, $totalAfterDiscount - $discountAmount);
            }

            // $order->total = $total;
            $order->total_after_discount = $totalAfterDiscount;
            $order->total_before_discount = $total;
            $order->total = $order->total_after_discount + $order->shipping_cost - ($order->points_amount ?? 0);
            $order->save();
            //  dd($order);

            // mark cart closed via repository
            // $this->repo->markCartConverted($cart);

            $this->action->deleteCart($user->id);
           return $order;  
        });

    }

    /**
     * Create order from a guest-provided Cart object (in-memory Cart built by CartService->mapGuestCartData).
     */
    public function createOrderFromGuestCart(\App\Models\Api\Ecommerce\Cart $cart, array $data): Order
    {
        return DB::transaction(function () use ($cart, $data) {
            // create main order record (guest => user_id null)
            $order = $this->repo->createGuestOrder($data);

            $total = 0;
            $totalAfterDiscount = 0;

            foreach ($cart->items as $cartItem) {
                $strategy = $this->resolver->resolve($cartItem);
                [$itemTotal, $itemTotalAfterDiscount] = $strategy->handle($cartItem, $order, $this->repo);
                $total += $itemTotal;
                $totalAfterDiscount += $itemTotalAfterDiscount;
            }

            // Guest orders do not support using loyalty points.

            if (!empty($data['coupon_code'])) {
                $discountAmount = $this->couponService->applyCouponToOrder($order, $data['coupon_code'], $totalAfterDiscount);
                $totalAfterDiscount = max(0, $totalAfterDiscount - $discountAmount);
            }

            $order->total_after_discount = $totalAfterDiscount;
            $order->total_before_discount = $total;
            $order->total = $order->total_after_discount + $order->shipping_cost - ($order->points_amount ?? 0);
            $order->save();

            return $order;
        });
    }

    // Resolver removed - strategies now use repository via handle signature


    /**
     * Calculate totals for a given Cart object without creating an order or mutating state.
     * Returns an array with breakdown: total_before_discount, total_after_discount, discount_amount,
     * points_amount, shipping_cost, total
     */
    public function calculateTotalsFromCart(Cart $cart, array $data = [], $user = null): array
    {
        $total = 0.0;
        $totalAfterDiscount = 0.0;

        foreach ($cart->items as $cartItem) {
            $total += (float) ($cartItem->total_before_discount ?? 0);
            $totalAfterDiscount += (float) ($cartItem->total_after_discount ?? 0);
        }

        $discountAmount = 0.0;
        if (!empty($data['coupon_code'])) {
            $discountAmount = $this->couponService->calculateCouponDiscount($data['coupon_code'], $totalAfterDiscount);
            $totalAfterDiscount = max(0, $totalAfterDiscount - $discountAmount);
        }

        $pointsAmount = 0.0;
        if (!empty($data['use_points']) && !empty($data['points_to_use']) && $user) {
            $pointsToUse = (int) $data['points_to_use'];
            $pointsAmount = $this->pointsService->calculatePointsAmount($user, $pointsToUse, $totalAfterDiscount);
        }

        // shipping cost same as repository default
        $shippingCost = 70;

        $finalTotal = $totalAfterDiscount + $shippingCost - $pointsAmount;

        return [
            'total_before_discount' => (float) $total,
            'total_after_discount' => (float) $totalAfterDiscount,
            'discount_amount' => (float) $discountAmount,
            'points_amount' => (float) $pointsAmount,
            'shipping_cost' => (float) $shippingCost,
            'total' => (float) max(0, $finalTotal),
        ];
    }

    /**
     * Convenience method: build preview for authenticated user's open cart.
     */
    public function previewForUser($user, array $data = []): array
    {
        $cart = $this->action->getUserOpenCart($user->id);
        return $this->calculateTotalsFromCart($cart, $data, $user);
    }






}
