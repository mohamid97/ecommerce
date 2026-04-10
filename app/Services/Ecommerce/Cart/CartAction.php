<?php
namespace App\Services\Ecommerce\Cart;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductOptions;
use App\Models\Api\Ecommerce\ProductVariant;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartAction{
    
    public $product;
    public $variant;
        // used method 
    public  function checkProductExists($productId){
        $this->product = Product::active()->find($productId);
        if(!$this->product) {
            throw new ModelNotFoundException(__('main.model_not_founded', ['model' => 'Product']));
        }

        return $this->product;
    }

    public function checkVariantExists($variantId){
        if($variantId){
            $this->variant = ProductVariant::find($variantId);
            if(!$this->variant) {
                throw new ModelNotFoundException(__('main.model_not_founded', ['model' => 'Product Variant']));
            }
        }
    }

    // check if user has product option 
    public function checkProductHasOption(){
        if($this->product->has_options){
            throw new \Exception(__('main.product_has_no_options'));
        }

        
    }


    // check stock amount 
    public function checkStockWithOption($quantity){
       $productOption = ProductVariant::where('product_id' , $this->product->id)->where('id' , $this->variant->id)->first();
       if($productOption->stock < $quantity){
          throw new \Exception(__('main.insufficient_stock_for_selected_variant'));
       }
       

    }

    public function checkStock($quantity){
        if($this->product->stock < $quantity){
            throw new \Exception(__('main.insufficient_stock'));
        }
    }





}