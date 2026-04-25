<?php

namespace App\Services\Ecommerce\Order;

use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\Order as OrderModel;
use App\Models\Api\Ecommerce\OrderItem;
use App\Models\Api\Ecommerce\OrderItemBatch;
use App\Models\Api\Ecommerce\ShipmentZone;
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
        $order->shipment_zone_id = $data['shipment_zone_id'] ?? null;
        $order->shipment_city_id = $data['shipment_city_id'] ?? null;
        $order->shipment_address = $data['shipment_address'] ?? null;
        $order->payment_method = $data['payment_method'] ?? null;
        $order->shipping_cost = ShipmentZone::find($order->shipment_zone_id)->price ?? 0;
        $order->total= 0;
        $order->tax = 0;
        $order->total_after_discount = 0;
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
