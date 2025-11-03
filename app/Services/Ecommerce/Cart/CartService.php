<?php
namespace App\Services\Ecommerce\Cart;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductOptions;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CartService{

    public $product;

    public function StoreToCart($userId , $dto){

        // check if product exist
        $this->checkProductExists($dto->product_id);
        //  check if product has option 
        if($dto->product_option_id){
            $this->checkProductHasOption();
        }
        $this->checkStock($dto->quantity);
          
        

    }











    // used method 
    protected function checkProductExists($productId){
        $this->product = Product::active()->find($productId);
        if(!$this->product) {
            throw new ModelNotFoundException('Product not found or not available.');
        }
    }

    // check if user has product option 
    protected function checkProductHasOption(){
        if($this->product->has_options){
            throw new \Exception('Product Has No Options.');
        }

        
    }


    protected function checkStock($quantity){
       $productOption = ProductOptions::where('product_id' , $this->product->id)->first();
       if($productOption->stock < $quantity){
          throw new \Exception('Insufficient stock for selected option.');
       }
       

    }


    


    
}