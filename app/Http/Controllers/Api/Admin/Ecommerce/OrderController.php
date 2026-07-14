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
use App\Http\Requests\Api\Admin\Ecommerce\OrderCreateGuestRequest;
use App\Http\Requests\Api\Admin\Ecommerce\OrderDeleteRequest;
use App\Services\Admin\Ecommerce\Order\AdminOrderService;

class OrderController extends Controller
{
    use ResponseTrait;

    public function __construct(
        protected AdminOrderService $adminOrderService
    ) {}

    public function all(OrderAllRequest $request)
    {
        try {
            $data = $request->validated();
            $orders = $this->adminOrderService->listOrders($data);
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
            $order = $this->adminOrderService->viewOrder($data);

            return $this->success(new OrderResource($order), __('main.order_details'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function userSummary(OrderUserSummaryRequest $request)
    {
        try {
            $data = $request->validated();
            $summary = $this->adminOrderService->userOrderSummary($data['user_id']);

            return $this->success(new UserOrderSummaryResource($summary), __('main.retrieved_successfully', ['model' => 'user order summary']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function updateStatus(OrderUpdateStatusRequest $request)
    {
        try {
            $data = $request->validated();
            $order = $this->adminOrderService->updateStatus($data);

            return $this->success(new OrderResource($order), __('main.updated_successfully', ['model' => 'Order']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function createGuest(OrderCreateGuestRequest $request)
    {
        try { 
            $data = $request->validated();

            $order = $this->adminOrderService->createGuestOrder($data);

            return $this->success(new OrderResource($order), __('main.created_successfully', ['model' => 'Order']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function delete(OrderDeleteRequest $request)
    {
        try {
            $this->adminOrderService->deleteOrder($request->validated());

            return $this->success(null, __('main.deleted_successfully', ['model' => 'Order']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }       
            
    }
    





}
