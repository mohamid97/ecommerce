<?php
namespace App\Services\Ecommerce\Cart;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductOptions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartAction{
    
    public $product;
        // used method 
    public  function checkProductExists($productId){
        $this->product = Product::active()->find($productId);
        if(!$this->product) {
            throw new ModelNotFoundException('Product not found or not available.');
        }

        return $this->product;
    }

    // check if user has product option 
    public function checkProductHasOption(){
        if($this->product->has_options){
            throw new \Exception('Product Has No Options.');
        }

        
    }


    // check stock amount 
    public function checkStockWithOption($quantity){
       $productOption = ProductOptions::where('product_id' , $this->product->id)->where('id' , $this->product->product_option_id)->first();
       if($productOption->stock < $quantity){
          throw new \Exception('Insufficient stock for selected option.');
       }
       

    }

    public function checkStock($quantity){
        if($this->product->noOption->stock < $quantity){
            throw new \Exception('Insufficient stock for selected option.');
        }
    }
}