<?php

namespace App\Services\Admin\Customer;

use App\Models\Api\Ecommerce\Order;
use App\Models\User;

class CustomerService 
{
    protected string $modelClass = User::class;

    public function all(array $data)
    {
        $query = User::query()
            ->where('type', 'user')
            ->with('profile')
            ->withCount('orders')
            ->withSum([
                'orders as total_spent' => function ($query) {
                    $query->where('payment_status', 'paid');
                },
            ], 'total');

        if (!empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($data['per_page'] ?? 15);
    }

    public function details(int $id)
    {
        return User::query()
            ->where('type', 'user')
            ->with([
                'profile',
                'latestOrders' => function ($query) {
                    $query->with('user')->withCount('items');
                },
            ])
            ->withCount([
                'orders',
                'orders as paid_orders_count' => function ($query) {
                    $query->where('payment_status', 'paid');
                },
            ])
            ->withSum([
                'orders as total_spent' => function ($query) {
                    $query->where('payment_status', 'paid');
                },
            ], 'total')
            ->findOrFail($id);
    }

    public function orders(array $data)
    {
        User::where('type', 'user')->findOrFail($data['user_id']);

        $query = Order::with('user')
            ->withCount('items')
            ->where('user_id', $data['user_id']);

        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        return $query->latest()->paginate($data['per_page'] ?? 15);
    }
}
