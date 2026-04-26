<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Api\Ecommerce\Order;
use App\Http\Resources\Api\Admin\Ecommerce\OrderResource;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ResponseTrait;

    public function all(Request $request)
    {
        try {
            $query = Order::with(['user']);

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            $orders = $query->latest()->paginate($request->per_page ?? 15);

            return $this->success([
                'orders' => OrderResource::collection($orders),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'total' => $orders->total(),
                    'per_page' => $orders->perPage(),
                ]
            ], __('main.orders'));

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function view(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:orders,id'
            ]);

            $order = Order::with([
                'user',
                'items.batches.stockMovment',
                'items.product',
                'items.variant',
                'items.bundel.bundelDetails.product'
            ])->findOrFail($request->id);

            return $this->success(new OrderResource($order), __('main.order_details'));

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
