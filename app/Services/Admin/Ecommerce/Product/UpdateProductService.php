<?php

namespace App\Services\Admin\Ecommerce\Product;

use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductOptionValue;
use App\Models\Api\Ecommerce\ProductShipement;
use App\Models\Api\Ecommerce\ProductVariant;
use Illuminate\Support\Facades\DB;

class UpdateProductService
{
    private $productId;

    public function __construct(protected DeleteEmptyProductOptionService $deleteEmptyProductOption) {}
    public function updateProductOption($data, $product): void
    {
        $this->productId = $product->id;
        
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


        $this->deleteEmptyProductOption->deleteEmptyOptions($this->productId);
         // check if product has option make product active
        if($product->options()->count() > 0){
            $product->update(['status' => 'active']);
        }







    }

    

    private function syncProductOptionValues($productOption, $option): void
    {
        // Get incoming value IDs for this option
        $incomingValueIds = collect($option['value_ids'] ?? [])->toArray();
        // Determine which option_value_ids will be removed
        $toDeleteValueIds = ProductOptionValue::where('product_option_id', $productOption->id)
            ->whereNotIn('option_value_id', $incomingValueIds)
            ->pluck('option_value_id')
            ->toArray();

        if (!empty($toDeleteValueIds)) {
            // Find variant IDs that reference these option value ids and belong to this product
            $variantIds = DB::table('variant_option_values')
                ->whereIn('option_value_id', $toDeleteValueIds)
                ->join('product_variants', 'variant_option_values.product_variant_id', '=', 'product_variants.id')
                ->where('product_variants.product_id', $this->productId)
                ->pluck('product_variant_id')
                ->unique()
                ->toArray();

            if (!empty($variantIds)) {
                ProductVariant::whereIn('id', $variantIds)->delete();
            }

            // Now delete the product option values
            ProductOptionValue::where('product_option_id', $productOption->id)
                ->whereIn('option_value_id', $toDeleteValueIds)
                ->delete();
        }
        
        // Create or update option values
        foreach ($option['value_ids'] ?? [] as $value) {
            ProductOptionValue::updateOrCreate(
                [
                    'product_option_id' => $productOption->id,
                    'option_value_id'   => $value,
                ],
                []
            );
        }

        
    }


    public function updateProductShipment($data , $productId){
        ProductShipement::where('product_id' , $productId)->update([
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