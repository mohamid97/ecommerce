<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Front\Order\AuthOrderStoreRequest;
use App\Http\Resources\Api\Front\Ecommerce\OrderResource;
use App\Services\Ecommerce\Order\OrderService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ResponseTrait;
    public function __construct(protected OrderService $service) {}

    public function store(AuthOrderStoreRequest $request)
    {
        try{
            $user = $request->user();
            $order = $this->service->createOrderFromCart($user, $request->validated());
            return $this->success(new OrderResource($order), __('main.created_successfully', ['model' => 'Order']));
        }catch(\Exception $e){
            return $this->error($e->getMessage(), 400);
        }
        
    }



}
