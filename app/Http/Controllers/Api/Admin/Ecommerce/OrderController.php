<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Api\Ecommerce\Order;
use App\Http\Resources\Api\Admin\Ecommerce\OrderResource;
use App\Http\Resources\Api\Admin\Ecommerce\OrderListResource;
use App\Http\Resources\Api\Admin\UserOrderSummaryResource;
use App\Traits\ResponseTrait;
use App\Http\Requests\Api\Admin\Ecommerce\OrderAllRequest;
use App\Http\Requests\Api\Admin\Ecommerce\OrderViewRequest;
use App\Http\Requests\Api\Admin\Ecommerce\OrderUpdateStatusRequest;
use App\Http\Requests\Api\Admin\Ecommerce\OrderUserSummaryRequest;
use App\Services\Ecommerce\Order\PointsService;
use App\Services\Admin\User\UserService;

class OrderController extends Controller
{
    use ResponseTrait;

    public function __construct(
        protected PointsService $pointsService,
        protected UserService $userService
    ) {}

    public function all(OrderAllRequest $request)
    {
        try {
            $query = Order::with(['user'])->withCount('items');

            $data = $request->validated();

            if (!empty($data['status'])) {
                $query->where('status', $data['status']);
            }

            if (!empty($data['user_id'])) {
                $query->where('user_id', $data['user_id']);
            }

            if (!empty($data['order_number'])) {
                $query->where('order_number', $data['order_number']);
            }

            if (!empty($data['search'])) {
                $search = $data['search'];
                $query->where(function ($q) use ($search) {
                        $q->where('guest_name', 'like', "%{$search}%")
                            ->orWhere('guest_email', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($uq) use ($search) {
                                $uq->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('username', 'like', "%{$search}%");
                            });
                });
            }

            if (!empty($data['sort']) && in_array($data['sort'], ['asc', 'desc'])) {
                $orders = $query->orderBy('created_at', $data['sort'])->paginate($data['per_page'] ?? 15);
            } else {
                $orders = $query->latest()->paginate($data['per_page'] ?? 15);
            }
            $collection = OrderListResource::collection($orders->getCollection());

            return $this->successPaginated($orders, $collection, 'items', __('main.orders'));

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function view(OrderViewRequest $request)
    {
        try {
            $data = $request->validated();

            $query = Order::with([
                'user',
                'items.batches.stockMovment',
                'items.product',
                'items.variant.variants.optionValue.option',
                'items.orderBundelItems.product',
                'items.orderBundelItems.variant.variants.optionValue.option',
                'items.bundel.bundelDetails.product',
            ]);

            $order = !empty($data['order_number'])
                ? $query->where('order_number', $data['order_number'])->firstOrFail()
                : $query->findOrFail($data['id']);

            return $this->success(new OrderResource($order), __('main.order_details'));

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function userSummary(OrderUserSummaryRequest $request)
    {
        try {
            $data = $request->validated();
            $summary = $this->userService->orderSummary($data['user_id']);

            return $this->success(new UserOrderSummaryResource($summary), __('main.retrieved_successfully', ['model' => 'user order summary']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function updateStatus(OrderUpdateStatusRequest $request)
    {
        try {
            $data = $request->validated();

            $order = !empty($data['order_number'])
                ? Order::where('order_number', $data['order_number'])->firstOrFail()
                : Order::findOrFail($data['id']);

            $order->status = $data['status'];
            $order->payment_status = $data['payment_status'];

            if ($order->status === 'delivered' && !$order->delivered_at) {
                $order->delivered_at = now();
            }
            $order->save();
            $this->pointsService->awardPointsForCompletedPaidOrder($order);

            return $this->success(new OrderResource($order->fresh('user')), __('main.updated_successfully', ['model' => 'Order']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

}
