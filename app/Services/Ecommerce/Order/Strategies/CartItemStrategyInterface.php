<?php

    /**
     * Handle converting a cart item to order item(s), allocate stock and return total price added to order subtotal.
     *
     * @param CartItem $cartItem
     * @param Order $order
     * @return float
     */
namespace App\Services\Ecommerce\Order\Strategies;

use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\Order;
use App\Services\Ecommerce\Order\OrderRepository;

interface CartItemStrategyInterface
{
    /**
     * Handle converting a cart item to order item(s), allocate stock and return total price added to order subtotal.
     *
     * @param CartItem $cartItem
     * @param Order $order
     * @param OrderRepository $repo
     * @return float
     */
    public function handle(CartItem $cartItem, Order $order, OrderRepository $repo): array;

    /**
     * Whether this strategy supports handling the given cart item.
     */
    public function supports(CartItem $cartItem): bool;
}
