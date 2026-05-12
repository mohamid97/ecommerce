<?php

namespace App\Services\Ecommerce\Order;

use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\Order as OrderModel;
use App\Models\Api\Ecommerce\OrderItem;
use App\Models\Api\Ecommerce\OrderItemBatch;
use App\Models\Api\Ecommerce\Order;

class OrderRepository
{
    public function saveOrder(OrderModel $order): OrderModel
    {
        $order->save();
        return $order;
    }

    public function createOrder($user, array $data): OrderModel
    {
        $order = new Order();
        $order->user_id = $user->id;
        $order->status = 'pending';
        $order->phone = $data['phone'] ?? $user->phone ?? null;
        $order->shipment_zone_id = $data['shipment_zone_id'] ?? null;
        $order->shipment_city_id = $data['shipment_city_id'] ?? null;
        $order->government = $data['government'] ?? null;
        $order->shipment_address = $data['shipment_address'] ?? null;
        $order->payment_method = $data['payment_method'] ?? null;
        // shipping cost is static (front will send government + full address)
        $order->shipping_cost = 70;
        $order->total= 0;
        $order->tax = 0;
        $order->total_after_discount = 0;
        $order->save();

        // generate human-friendly order number based on id
        $order->order_number = 'ORD-' . str_pad($order->id ?? 0, 6, '0', STR_PAD_LEFT);
        $order->save();

        return $order;
    }

    /**
     * Create a guest order (no user_id) from provided data.
     */
    public function createGuestOrder(array $data): OrderModel
    {
        $order = new Order();
        $order->user_id = null;
        $order->status = 'pending';
        $order->guest_name = $data['name'] ?? null;
        $order->guest_email = $data['email'] ?? null;
        $order->phone = $data['phone'] ?? null;
        $order->shipment_zone_id = $data['shipment_zone_id'] ?? null;
        $order->shipment_city_id = $data['shipment_city_id'] ?? null;
        $order->government = $data['government'] ?? null;
        $order->shipment_address = $data['shipment_address'] ?? null;
        $order->payment_method = $data['payment_method'] ?? null;
        // shipping cost is static
        $order->shipping_cost = 70;
        $order->total = 0;
        $order->tax = 0;
        $order->total_after_discount = 0;
        $order->save();

        // generate human-friendly order number based on id
        $order->order_number = 'ORD-' . str_pad($order->id ?? 0, 6, '0', STR_PAD_LEFT);
        $order->save();

        return $order;
    }

    public function createOrderItem(array $data): OrderItem
    {
        return OrderItem::create($data);
    }

    public function createOrderItemBatch(array $data): OrderItemBatch
    {
        return OrderItemBatch::create($data);
    }

    public function markCartConverted(Cart $cart): void
    {
        $cart->status = 'converted';
        $cart->save();
    }
}
