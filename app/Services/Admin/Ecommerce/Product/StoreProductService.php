<?php

namespace App\Services\Ecommerce\Product;
use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductOptionValue;

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
            
            $productOption = ProductOption::create(
                [
                    'product_id' => $id,
                    'option_id' => $option['option_id'],
                ]
            );
            foreach($option['value_ids'] as $value_id){
                ProductOptionValue::create(
                    [
                        'product_option_id' => $productOption->id,
                        'option_value_id' => $value_id,
                    ]
                );

                
            } // end store product option value


            
        }



        
    } // end store product option



    
}


