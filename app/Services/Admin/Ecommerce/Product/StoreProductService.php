<?php

namespace App\Services\Admin\Ecommerce\Product;
use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductOptionValue;
use App\Models\Api\Ecommerce\ProductShipement;

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


    public function storeProductShipment($data , $productId){
        ProductShipement::create([
            'product_id'=>$productId,
            'variant_id'=>$data['variant_id']?? null,
            'length'=>$data['length'],
            'width'=>$data['width'],
            'height'=>$data['height'],
            'weight'=>$data['weight'],
            'min_estimated_delivery'=>$data['min_estimated_delivery'],
            'max_estimated_delivery'=>$data['max_estimated_delivery']
        ]);
    }
    



    
}


