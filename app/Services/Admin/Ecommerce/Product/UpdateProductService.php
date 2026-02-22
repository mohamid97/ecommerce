<?php

namespace App\Services\Admin\Ecommerce\Product;

use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductOptionValue;
use App\Models\Api\Ecommerce\ProductShipement;

class UpdateProductService
{
    private $productId;

    public function updateProductOption($data, $id): void
    {
        $this->productId = $id;
        
        // Get incoming option IDs
        $incomingOptionIds = collect($data)->pluck('option_id')->toArray();
        
        // Delete options that are not in the incoming data
        ProductOption::where('product_id', $this->productId)
            ->whereNotIn('option_id', $incomingOptionIds)
            ->each(function ($productOption) {
                $productOption->values()->delete();
                $productOption->delete();
            });
        
        // Process each option from incoming data
        foreach ($data as $option) {
            // Create or update the product option
            $productOption = ProductOption::updateOrCreate(
                [
                    'product_id' => $this->productId,
                    'option_id'  => $option['option_id'],
                ]
            );
            
            // Sync the option values
            $this->syncProductOptionValues($productOption, $option);
        }
    }

    

    private function syncProductOptionValues($productOption, $option): void
    {
        // Get incoming value IDs for this option
        $incomingValueIds = collect($option['value_ids'] ?? [])->toArray();

        
        // Delete values that are not in the incoming data
        ProductOptionValue::where('product_option_id', $productOption->id)
            ->whereNotIn('option_value_id', $incomingValueIds)
            ->delete();
        
        // Create or update option values
        foreach ($option['value_ids'] ?? [] as $value) {
            ProductOptionValue::updateOrCreate(
                [
                    'product_option_id' => $productOption->id,
                    'option_value_id'   => $value,
                ],
                [

                ]
            );
        }

        
    }


    public function updateProductShipment($data , $productId){
        ProductShipement::where('product_id' , $productId)->update([
            'product_id'=>$productId,
            'variant_id'=>$data['variant_id']?? null,
            'length'=>$data['length'],
            'width'=>$data['width'],
            'height'=>'height',
            'weight'=>$data['weight'],
            'min_estimated_delivery'=>$data['min_estimated_delivery'],
            'max_estimated_delivery'=>$data['max_estimated_delivery']
        ]);
    }
}