<?php
namespace App\Services\Admin\Ecommerce\Bundel\Actions;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;

class ValidateBundel{
    public function validateBundelDetails($bundel_id , $dto){
        // loop all product inside bundel
            foreach ($dto->products as $item) {

                $product = Product::findOrFail($item['product_id']);

                $hasVariants = (bool) $product->has_options;
   
                if ($hasVariants) { // check if produt has varaint
                    if (empty($item['variant_ids'])) {
                        throw new \Exception("Product {$product->id} requires variant_ids");
                    }
                    // need to check if product has varaint 
                    foreach($item['variant_ids'] as $variant_id){
                        if(ProductVariant::where('product_id' , $product->id)->where('id' , $variant_id)->exists()){
                            throw new \Exception("This Varaint Not Belong To This Product");

                        }
                    } // end loop for varaint

                }



                $variantIds = $item['variant_ids'];
            }

        
     }

      
}