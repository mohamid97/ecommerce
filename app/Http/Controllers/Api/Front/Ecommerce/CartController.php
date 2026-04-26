<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\DTO\Ecommerce\Cart\RemoveFromCartDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Ecommerce\Cart\CartStoreRequest;
use App\Http\Requests\Api\Ecommerce\Cart\DeleteFromCartRequest;
use App\Http\Resources\Api\Front\Ecommerce\CartResource;
use App\Services\Ecommerce\Cart\CartService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\CartItem;

class CartController extends Controller
{
    use ResponseTrait;
    public function __construct(
        private CartService $cartService  
    ) {}

    public function viewCart(Request $request){
        // dd('dsds');
        $userId = $request->user()->id;
        
        $cart = Cart::with([
            'items.product',
            // 'items.variant.variants.optionValue.option',
            // 'items.bundel',
            // 'items.cartBundelItems.product',
            // 'items.cartBundelItems.variant.variants.optionValue.option'
        ])->where('user_id' , $userId)->first();
        
        if(!$cart){
            return $this->success(null , 200);
        }

        
        return $this->success(new CartResource($cart) , 200);

    }

    public function addToCart(CartStoreRequest $request)    
    {
       
        try {
            DB::beginTransaction();
            $dto = AddToCartDTO::fromRequest($request->validated());
            $userId = $request->user()->id;
            $this->cartService->StoreToCart($userId , $dto);
            DB::commit();
            return $this->success( null , __('main.stored_successfully' , ['model' => 'Item']) , 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage() , 422);
        }
    }

    public function deleteFromCart(DeleteFromCartRequest $request){

       try{
            DB::beginTransaction();
            $userId = $request->user()->id;
            $dto = RemoveFromCartDTO::fromRequest($request->validated());
            $this->cartService->RemoveFromCart($userId , $dto);
            DB::commit();
            return $this->success(null , __('main.deleted_successfully' , ['model' => 'Item']) , 200);
       }catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage() , 422);
        }


        

    } // delete from cart



    public function deleteAllFromCart(Request $request){
        $userId = $request->user()->id;
        // need to delete car also 
        Cart::where('user_id', $userId)->delete();
        return $this->success(null , 200);
     }







}
