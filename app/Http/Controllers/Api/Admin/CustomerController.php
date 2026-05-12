<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Customers\CustomerAllRequest;
use App\Http\Requests\Api\Admin\Customers\CustomerOrdersRequest;
use App\Http\Requests\Api\Admin\Customers\CustomerViewRequest;
use App\Http\Resources\Api\Admin\CustomerDetailsResource;
use App\Http\Resources\Api\Admin\CustomerResource;
use App\Http\Resources\Api\Admin\Ecommerce\OrderListResource;
use App\Services\Admin\Customer\CustomerService;
use App\Traits\ResponseTrait;

class CustomerController extends Controller
{
    use ResponseTrait;

    protected CustomerService $service;

    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }

    public function all(CustomerAllRequest $request)
    {
        try {
            $data = $request->validated();
            $customers = $this->service->all($data);
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
            $customer = $this->service->details($data['id']);

            return $this->success(new CustomerDetailsResource($customer), __('main.model_details', ['model' => 'customer']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function orders(CustomerOrdersRequest $request)
    {
        try {
            $data = $request->validated();
            $orders = $this->service->orders($data);
            $collection = OrderListResource::collection($orders->getCollection());

            return $this->successPaginated($orders, $collection, 'items', __('main.orders'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
