<?php
namespace App\Services\Ecommerce\Cart;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\NoOptionStock;
use App\Models\Api\Ecommerce\ProductOptions;

class CartRepository{
    public function createOrUpdateCard($userId,  AddToCartDTO $dto){
        
       // if has no cart create new one
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId] 
        );

   
        CartItem::createOrUpdate(
            ['cart_id'=>$cart->id , 'product_id'=>$dto->product_id],
            [
                'quantity'=>$dto->quantity, 
                'variant_id'=>($dto->varaint_id) ?$dto->varaint_id :null,
                'total_before_discount'=>$this->getProductPrice($dto),
                'total_after_discount'=>$this->getProductPrice($dto)
            ]
        );
        
        return $cart;
        
    }

    private function getProductPrice($dto){
        $productPrice = 0;
        $product = Product::find($dto->product->id);
        if($product->has_options){
            $productOptions = ProductOptions::where('product_id' , $dto->product_id)->where('id' , $dto->product_option_id)->first();
            $productPrice = $productOptions->price;
        }else{
            $productNoOption = NoOptionStock::where('product_id' , $dto->product->id)->first();
            $productPrice = $productNoOption->base_price;

        }


        return $productPrice;
        
       
        

    }



    public function removeFromCart($userId , $dto){
        $cart = Cart::where('user_id' , $userId)->first();
        if($cart){
            CartItem::where('cart_id' , $cart->id)
                    ->where('product_id' , $dto->productId)
                    ->when($dto->variantId , function($query) use ($dto){
                        $query->where('varaint_id' , $dto->variantId);
                    })
                    ->delete();
        }

        // check if cart is empty then delete it
        if($cart->items()->count() == 0){
            $cart->delete();
        }

        
    }


  
}