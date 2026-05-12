<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Customers\CustomerAllRequest;
use App\Http\Requests\Api\Admin\Customers\CustomerOrdersRequest;
use App\Http\Requests\Api\Admin\Customers\CustomerViewRequest;
use App\Http\Resources\Api\Admin\CustomerDetailsResource;
use App\Http\Resources\Api\Admin\CustomerResource;
use App\Http\Resources\Api\Admin\Ecommerce\OrderListResource;
use App\Models\Api\Ecommerce\Order;
use App\Models\User;
use App\Traits\ResponseTrait;

class CustomerController extends Controller
{
    use ResponseTrait;

    public function all(CustomerAllRequest $request)
    {
        try {
            $data = $request->validated();

            $query = User::query()
                ->where('type', 'user')
                ->with('profile')
                ->withCount('orders')
                ->withSum([
                    'orders as total_spent' => fn ($query) => $query->where('payment_status', 'paid'),
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

            $customers = $query->latest()->paginate($data['per_page'] ?? 15);
            $collection = CustomerResource::collection($customers->getCollection());

            return $this->successPaginated($customers, $collection, 'items', __('main.list_successfully', ['model' => 'customers']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function view(CustomerViewRequest $request)
    {
        try {
            $data = $request->validated();

            $customer = User::query()
                ->where('type', 'user')
                ->with([
                    'profile',
                    'latestOrders' => fn ($query) => $query->with('user')->withCount('items'),
                ])
                ->withCount([
                    'orders',
                    'orders as paid_orders_count' => fn ($query) => $query->where('payment_status', 'paid'),
                ])
                ->withSum([
                    'orders as total_spent' => fn ($query) => $query->where('payment_status', 'paid'),
                ], 'total')
                ->findOrFail($data['id']);

            return $this->success(new CustomerDetailsResource($customer), __('main.model_details', ['model' => 'customer']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function orders(CustomerOrdersRequest $request)
    {
        try {
            $data = $request->validated();

            User::where('type', 'user')->findOrFail($data['user_id']);

            $query = Order::with('user')
                ->withCount('items')
                ->where('user_id', $data['user_id']);

            if (!empty($data['status'])) {
                $query->where('status', $data['status']);
            }

            $orders = $query->latest()->paginate($data['per_page'] ?? 15);
            $collection = OrderListResource::collection($orders->getCollection());

            return $this->successPaginated($orders, $collection, 'items', __('main.orders'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
