<?php 
namespace App\Services\Admin\Ecommerce\Product\Actions\Variant;

use App\Models\Api\Admin\Lang;
use App\Models\Api\Ecommerce\ProductVaraintImages;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\VariantOptionValue;
use App\Models\Api\Ecommerce\OptionValue;
use App\Models\Api\Ecommerce\ProductOption;

class StoreVaraintAction
{

    public function storeVariant($dto){
        $this->validateDiscount($dto->discountType, $dto->discount , $dto->salePrice);
        $this->checkIfVariantExists($dto->productId, $dto->optionValueIds);
          
        $productVaraint = ProductVariant::create([
            'product_id' => $dto->productId,
            'sale_price' => $dto->salePrice,
            'discount' => $dto->discount,
            'discount_type' => $dto->discountType,
            'sku' => $dto->sku,
            'barcode' => $dto->barcode,
            'length' => $dto->length,
            'weight' => $dto->weight,
            'width' => $dto->width,
            'height' => $dto->height,
            // 'stock' => $dto->stock,
            'delivery_time' => $dto->deliveryTime,
            'max_time' => $dto->maxTime,
        ]);
        $this->storeVariantTranslations($dto, $productVaraint);

        // store variant option value
        foreach($dto->optionValueIds as $optionValueId){
            VariantOptionValue::create([
                'product_variant_id' => $productVaraint->id,
                'option_id' => OptionValue::find($optionValueId)->option_id,
                'option_value_id' => $optionValueId,
            ]);
        }

        // store variant images
        if($dto->imagesIds){
            foreach($dto->imagesIds as $imageId){
                
                ProductVaraintImages::create([
                    'variant_id' => $productVaraint->id,
                    'image_id' => $imageId,
                ]);
            }
        }

        return $productVaraint;
        


    }

    // validate discount value based on type
    public function validateDiscount($type, $value, $salePrice){
        if($type == 'percentage' && ($value < 0 || $value > 100)){
            throw new \Exception('Invalid discount percentage value');
        }
        if($type == 'fixed' && ($value < 0 || $value > $salePrice)){
            throw new \Exception('Invalid fixed discount value');
        }
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



     // their two tale variant_option_values and product varaint we need to check that the combination of option value ids is not repeated for the same product variant
        // normalize
        $option_value_ids = array_values((array) $option_value_ids);

        if (empty($option_value_ids)) {
            throw new \Exception('No option values provided for variant');
        }

        // ensure all option values exist and gather their option ids
        $values = OptionValue::whereIn('id', $option_value_ids)->get();
        if ($values->count() !== count($option_value_ids)) {
            throw new \Exception('One or more option values do not exist');
        }

      
        $optionIds = $values->pluck('option_id')->toArray();
        

        // ensure each submitted option value belongs to a different option
        if (count(array_unique($optionIds)) !== count($optionIds)) {
            throw new \Exception('Multiple option values belong to the same option');
        }

        // ensure the options are attached to the product
        $productOptionIds = ProductOption::where('product_id', $product_id)->pluck('option_id')->toArray();
        foreach ($optionIds as $optId) {
            if (! in_array($optId, $productOptionIds, true)) {
                throw new \Exception('One or more option values do not belong to this product');
            }
        }

        // check existing variant with same combination
        $existingVariant = ProductVariant::where('product_id', $product_id)
            ->whereHas('variants', function ($query) use ($option_value_ids) {
                $query->whereIn('option_value_id', $option_value_ids)
                    ->groupBy('product_variant_id')
                    ->havingRaw('COUNT(DISTINCT option_value_id) = ?', [count($option_value_ids)]);
            })
            ->first();

        if ($existingVariant) {
            throw new \Exception('Variant already exists');
        }
    }


}