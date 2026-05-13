<?php

namespace App\Services\Admin\Ecommerce\Order;

use App\Models\Api\Ecommerce\Order as OrderModel;
use App\Services\Ecommerce\Order\OrderService as EcommerceOrderService;
use App\Services\Ecommerce\Order\PointsService;
use App\Services\Admin\User\UserService;

class AdminOrderService
{
    public function __construct(
        protected AdminOrderRepository $repository,
        protected EcommerceOrderService $orderService
    ) {}

    /**
     * Return paginated orders according to admin filters.
     */
    public function listOrders(array $filters)
    {
        return $this->repository->paginateOrders($filters);
    }

    /**
     * View single order with relations.
     */
    public function viewOrder(array $data): OrderModel
    {
        $query = OrderModel::with([
            'user',
            'items.batches.stockMovment',
            'items.product',
            'items.variant.variants.optionValue.option',
            'items.orderBundelItems.product',
            'items.orderBundelItems.variant.variants.optionValue.option',
            'items.bundel.bundelDetails.product',
        ]);

        return !empty($data['order_number'])
            ? $query->where('order_number', $data['order_number'])->firstOrFail()
            : $query->findOrFail($data['id']);
    }

    /**
     * Return order summary for a given user.
     */
    public function userOrderSummary(int $userId)
    {
        return app(UserService::class)->orderSummary($userId);
    }

    /**
     * Update order status and payment status.
     */
    public function updateStatus(array $data): OrderModel
    {
        $order = $this->repository->findByIdOrNumber($data);

        $order->status = $data['status'];
        $order->payment_status = $data['payment_status'];

        if ($order->status === 'delivered' && !$order->delivered_at) {
            $order->delivered_at = now();
        }

        $this->repository->save($order);

        // award points if necessary
        app(PointsService::class)->awardPointsForCompletedPaidOrder($order);

        return $order->fresh('user');
    }

    /**
     * Create a guest order from admin UI payload.
     * Accepts the same payload shape as front GuestOrderStoreRequest.
     */
    public function createGuestOrder(array $data): OrderModel
    {
        $products = [];
        $bundles = [];

        foreach ($data['items'] as $item) {
            if (!empty($item['bundel_id'])) {
                $bundles[] = [
                    'bundle_id' => $item['bundel_id'],
                    'quantity' => $item['quantity'],
                    'bundle_items' => $item['bundle_items'] ?? [],
                ];
            } else {
                $products[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                ];
            }
        }

        $cart = app(\App\Services\Ecommerce\Cart\CartService::class)->mapGuestCartData([
            'products' => $products,
            'bundles' => $bundles,
        ]);

        $order = $this->orderService->createOrderFromGuestCart($cart, $data);

        $this->repository->attachAdminMeta($order, []);

        return $order;
    }

    
}
