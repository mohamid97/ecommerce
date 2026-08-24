<?php

namespace App\Services\Ecommerce\Order;

use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\Order;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
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
        protected PointsService $pointsService,
        protected \App\Services\Ecommerce\Shipment\ShipmentCalculationService $shipmentCalculation
    ) {}


    // check MOQ Multiple of a number for a given product/variant context for un authenticated user
    public function checkMoqForGuest($productId, $variantId, $quantity)
    {

        $product = Product::find($productId);
        $moq = $product->moq ?? 1;

        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            $moq = $variant->moq ?? $moq;
        } 
        if ($quantity < $moq || $quantity % $moq !== 0) {
            throw new \Exception("Quantity must be a multiple of the minimum order quantity ($moq).");
        }

    }
 

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
            if (!empty($data['points'])) {
                $pointsToUse = (int) $data['points'];
                $pointsAmount = $this->pointsService->applyPointsToOrder($user, $order, $pointsToUse, $totalAfterDiscount);
            }
           

            // apply coupon (promotion) if provided
            if (!empty($data['coupon_code'])) {
                $discountAmount = $this->couponService->applyCouponToOrder($order, $data['coupon_code'], $totalAfterDiscount);
                $totalAfterDiscount = max(0, $totalAfterDiscount - $discountAmount);
            }

            // $order->total = $total;
            $order->total_after_discount = $totalAfterDiscount;
            $order->total_before_discount = $total;
            // calculate real shipping cost
            $shippingCost = $this->shipmentCalculation->calculate($cart->items, $data['zone_id'] ?? null)['shipping_cost'];
            $order->shipping_cost = $shippingCost;
            $order->total = $order->total_after_discount + $order->shipping_cost - ($order->points_amount ?? 0);
            $order->save();

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
            // calculate real shipping cost
            $shippingCost = $this->shipmentCalculation->calculate($cart->items, $data['zone_id'] ?? null)['shipping_cost'];
            $order->shipping_cost = $shippingCost;
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
        $totalBeforeDiscount = 0.0;
        $totalAfterDiscount = 0.0;

        foreach ($cart->items as $cartItem) {
            $totalBeforeDiscount += (float) ($cartItem->total_before_discount ?? 0);
            $totalAfterDiscount += (float) ($cartItem->total_after_discount ?? 0);
        }

        // Use the pre-coupon base amount for both coupon and points calculations
        $baseAmount = $totalAfterDiscount;

        $discountAmount = 0.0;
        if (!empty($data['coupon_code'])) {
            $discountAmount = $this->couponService->calculateCouponDiscount($data['coupon_code'], $baseAmount);
        }

        $pointsAmount = 0.0;
        if (!empty($data['points']) && $user) {
            $pointsToUse = (int) $data['points'];
            $pointsAmount = $this->pointsService->calculatePointsAmount($user, $pointsToUse, $baseAmount);
        }

        // apply coupon to produce post-discount total (matching createOrder flow)
        $totalAfterPreview= max(0, $baseAmount - $discountAmount - $pointsAmount);

        // calculate real shipping cost based on cart items and selected zone
        $shippingCost = $this->shipmentCalculation->calculate($cart->items, $data['zone_id'] ?? null)['shipping_cost'];

        $finalTotal = ( $totalAfterPreview + $shippingCost );

        return [
            'total_before_discount' => (float) $totalBeforeDiscount,
            'total_after_discount' => (float) $totalAfterDiscount,
            'total_after_preview' => (float) $totalAfterPreview,
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
