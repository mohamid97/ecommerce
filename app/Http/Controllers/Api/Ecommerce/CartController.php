<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Ecommerce\Cart\CartStoreRequest;
use App\Services\Ecommerce\Cart\CartService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    use ResponseTrait;
    public function addToCart(CartStoreRequest $request , CartService $cart){
        try {
            DB::beginTransaction();
            $dto = AddToCartDTO::fromRequest($request->validated());
            $userId = $request->user->id ?? 'guest';
            $cart->StoreToCart($userId , $dto);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
           return  $this->error($e->getMessage() , 422);

        }

    }
}