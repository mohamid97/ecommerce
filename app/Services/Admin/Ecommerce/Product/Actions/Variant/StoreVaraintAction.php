<?php 
namespace App\Services\Admin\Ecommerce\Product\Actions\Variant;

use App\Models\Api\Admin\Lang;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\VariantOptionValue;

class StoreVaraintAction
{

    public function storeVariant($dto){
        $this->checkIfVariantExists($dto->product_id, $dto->optionValueIds);

        $productVaraint = ProductVariant::create([
            'product_id' => $dto->product_id,
            'sale_price' => $dto->salePrice,
            'discount' => $dto->discount,
            'discount_type' => $dto->discountType,
            'sku' => $dto->sku,
            'barcode' => $dto->barcode,
            'length' => $dto->length,
            'weight' => $dto->weight,
            'width' => $dto->width,
            'height' => $dto->height,
            'stock' => $dto->stock,
            'delivery_time' => $dto->deliveryTime,
            'max_time' => $dto->maxTime,
        ]);
        $this->storeVariantTranslations($dto, $productVaraint);

        // store variant option value
        foreach($dto->optionValueIds as $optionValueId){
            VariantOptionValue::create([
                'product_variant_id' => $productVaraint->id,
                'option_value_id' => $optionValueId,
            ]);
        }

        return $productVaraint;
        


    }

    public function storeVariantTranslations($dto, $productVaraint){
        foreach(Lang::all() as $locale){
            if(isset($dto->title[$locale->code])){
                $productVaraint->translateOrNew($locale->code)->title = $dto->title[$locale->code];
            }
            if(isset($dto->slug[$locale->code])){
                $productVaraint->translateOrNew($locale->code)->slug = $dto->slug[$locale->code];
            }
            if(isset($dto->des[$locale->code])){
                $productVaraint->translateOrNew($locale->code)->des = $dto->des[$locale->code];
            }
            if(isset($dto->metaTitle[$locale->code])){
                $productVaraint->translateOrNew($locale->code)->meta_title = $dto->metaTitle[$locale->code];
            }
            if(isset($dto->metaDes[$locale->code])){
                $productVaraint->translateOrNew($locale->code)->meta_des = $dto->metaDes[$locale->code];
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

            if($existingVariant){
                throw new \Exception('Variant already exists');
            }
    }


}