<?php

namespace App\Services\Admin\Ecommerce\Order;

use App\Models\Api\Ecommerce\Order as OrderModel;

class AdminOrderRepository
{
    /**
     * Create guest order using the existing Ecommerce OrderRepository.
     */
    public function createGuestOrder(array $data): OrderModel
    {
        return app(\App\Services\Ecommerce\Order\OrderRepository::class)->createGuestOrder($data);
    }

    /**
     * Placeholder to attach admin-specific metadata to an order.
     */
    public function attachAdminMeta(OrderModel $order, array $meta): OrderModel
    {
        return $order;
    }

    /**
     * Build and return paginated orders based on filters.
     */
    public function paginateOrders(array $filters)
    {
        $query = OrderModel::with(['user'])->withCount('items');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        if (!empty($filters['order_number'])) {
            $query->where('order_number', $filters['order_number']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('guest_name', 'like', "%{$search}%")
                    ->orWhere('guest_email', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
            });
        }   

        if (!empty($filters['orderDirection']) && in_array($filters['orderDirection'], ['asc', 'desc'])) {
            return $query->orderBy($filters['orderBy'] ?? 'created_at', $filters['orderDirection'])->paginate($filters['paginate'] ?? 15);
        }

        return $query->latest()->paginate($filters['paginate'] ?? 15);
    }

    /**
     * Find order by id or order_number.
     */
    public function findByIdOrNumber(array $data): OrderModel
    {
        $query = OrderModel::query();

        if (!empty($data['order_number'])) {
            return $query->where('order_number', $data['order_number'])->firstOrFail();
        }

        return $query->findOrFail($data['id']);
    }

    /**
     * Save order instance.
     */
    public function save(OrderModel $order): OrderModel
    {
        $order->save();
        return $order;
    }
}
