<?php

namespace App\Services\Admin\Ecommerce\Product;
use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductOptionValue;
use App\Models\Api\Ecommerce\ProductShipement;
use App\Models\Api\Ecommerce\OptionValue;

class StoreProductService
{


    // public function completeProductData($data , $id){
        
    //         NoOptionStock::updateOrCreate( 
    //             [
    //                 'product_id' => $id,
                   
    //             ],
    //             [
    //                 'sku' => $data['sku'],
    //                 'stock' => $data['stock'],
    //                 'base_price' => $data['base_price'],
    //             ]
    //         );


        
    // }


    public function addProductOption($data , $id):void{

        foreach ($data as $option) {


            $productOption = ProductOption::firstOrCreate(
                [
                    'product_id' => $id,
                    'option_id' => $option['option_id'],
                ]
            );

            foreach ($option['value_ids'] ?? [] as $value_id) {
                $isValid = OptionValue::where('id', $value_id)
                    ->where('option_id', $option['option_id'])
                    ->exists();

                if (! $isValid) {
                    continue;
                }

                ProductOptionValue::firstOrCreate([
                    'product_option_id' => $productOption->id,
                    'option_value_id' => $value_id,
                ]);
            } // end store product option value


            
        }



        
    } // end store product option


    public function storeProductShipment($data , $productId){
        ProductShipement::create([
            'product_id'=>$productId,
            'variant_id'=>$data['variant_id']?? null,
            'length'=>$data['length'] ?? null,
            'width'=>$data['width'] ?? null,
            'height'=>$data['height'] ?? null,
            'weight'=>$data['weight'] ?? null,
            'min_estimated_delivery'=>$data['min_estimated_delivery'] ?? null,
            'max_estimated_delivery'=>$data['max_estimated_delivery'] ?? null
        ]);
    }
    



    
}


