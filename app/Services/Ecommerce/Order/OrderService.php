<?php

namespace App\Services\Ecommerce\Order;

use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\StockMovment;
use App\Models\Api\Ecommerce\Order;
use App\Models\Api\Ecommerce\OrderItem;
use App\Models\Api\Ecommerce\OrderItemBatch;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ShipmentZone;
use App\Services\Ecommerce\Order\Strategies\VariantItemStrategy;
use App\Services\Ecommerce\Order\Strategies\BundleItemStrategy;
use App\Services\Ecommerce\Order\Strategies\SimpleProductItemStrategy;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected OrderRepository $repo,
        protected OrderAction $action,
        protected OrderStrategyResolver $resolver
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

            // points handling (optional)
            if (!empty($data['use_points']) && !empty($data['points_to_use'])) {
                $order->points_used = (int) $data['points_to_use'];
                // compute points_amount using conversion table if exists
                $order->points_amount = 0; // implement conversion as needed
            }

            $order->total = $total;
            $order->total_after_discount = $totalAfterDiscount;
            $order->total = $totalAfterDiscount + $order->shipping_cost + $order->tax - ($order->points_amount ?? 0);
            $order->save();

            // mark cart closed via repository
            $this->repo->markCartConverted($cart);

            return $order->load([
                'items.batches.stockMovment',
                'items.product',
                'items.variant',
                'items.bundel.bundelDetails.product',
                'items.bundel.bundelDetails.variants',
            ]);
        });
    }

    // Resolver removed - strategies now use repository via handle signature
}
