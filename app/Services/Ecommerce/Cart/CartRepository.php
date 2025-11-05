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
                'product_option_id'=>($dto->product_option_id) ?$dto->product_option_id :null,
                'price'=>$this->getProductPrice($dto)
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



  
}