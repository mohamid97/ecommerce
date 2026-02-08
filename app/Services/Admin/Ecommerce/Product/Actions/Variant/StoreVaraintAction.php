<?php 
namespace App\Services\Admin\Ecommerce\Product\Actions\Variant;

use App\Models\Api\Ecommerce\ProductVariant;

class StoreVariantAction
{
    public function storeVariant($dto){
        $this->checkIfVariantExists($dto->product_id, $dto->option_value_ids);
        $productVaraint = ProductVariant::create([
            'product_id' => $dto->product_id,
            'option_value_ids' => $dto->option_value_ids,
            'cost_price' => $dto->cost_price,
            'sale_price' => $dto->sale_price,
            'discount' => $dto->discount,
            'discount_type' => $dto->discount_type,
            'sku' => $dto->sku,
            'barcode' => $dto->barcode,
            'length' => $dto->length,
            'weight' => $dto->weight,
            'width' => $dto->width,
            'height' => $dto->height,
            'stock' => $dto->stock,
            'price' => $dto->price,
            'delivery_time' => $dto->delivery_time,
            'max_time' => $dto->max_time,
        ]);
        $this->storeVariantTranslations($dto, $productVaraint);

        // store images if exist
        

        // store option value ids relation
        $productVaraint->optionValueIds()->attach($dto->optionValueIds);

    }

    public function storeVariantTranslations($dto, $productVaraint){
        foreach($dto->titles as $locale => $title){
            $productVaraint->translateOrNew($locale)->title = $title;
            if(isset($dto->des[$locale])){
                $productVaraint->translateOrNew($locale)->des = $dto->des[$locale];
            }
            if(isset($dto->meta_title[$locale])){
                $productVaraint->translateOrNew($locale)->meta_title = $dto->meta_title[$locale];
            }
            if(isset($dto->meta_des[$locale])){
                $productVaraint->translateOrNew($locale)->meta_des = $dto->meta_des[$locale];
            }
            $productVaraint->save();
        }
    } // end store variant translations

    public function checkIfVariantExists($product_id, $option_value_ids){

     // variant_option_values this tale has product_variant_id and option_value_id
     // need to ccheck that options ids values are not repeated for the same product variant

     // their two tale variant_option_values and product varaint we need to check that the combination of option value ids is not repeated for the same product variant
        $existingVariant = ProductVariant::where('product_id', $product_id)
            ->whereHas('variants', function($query) use ($option_value_ids) {
                // that ok ut also must productVaraintid must same as you know variant_option_values table has product_variant_id and option_value_id we need to check that the combination of option value ids is not repeated for the same product variant
                $query->whereIn('option_value_id', $option_value_ids)
                    ->groupBy('product_variant_id')
                    ->havingRaw('COUNT(DISTINCT option_value_id) = ?', [count($option_value_ids)]);
            })
            ->first();
    }


}