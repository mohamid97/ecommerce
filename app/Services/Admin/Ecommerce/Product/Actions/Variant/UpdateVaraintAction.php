<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Variant;

use App\Models\Api\Admin\Lang;
use App\Models\Api\Ecommerce\ProductVaraintImages;
use App\Models\Api\Ecommerce\ProductVariant;

class UpdateVaraintAction
{
    public function updateVariant($dto){
        $productVaraint = ProductVariant::findOrFail($dto->id);

        $data = array_filter([
            'sale_price' => $dto->sale_price,
            'discount_value' => $dto->discount,
            'discount_type' => $dto->discount_type,
            'sku' => $dto->sku,
            'barcode' => $dto->barcode,
            'moq' => $dto->moq,
            'length' => $dto->length,
            'weight' => $dto->weight,
            'width' => $dto->width,
            'height' => $dto->height,
            'delivery_time' => $dto->delivery_time,
            'max_time' => $dto->max_time,
        ], fn ($value) => $value !== null);

        if (!empty($data)) {
            $productVaraint->update($data);
        }

        $this->updateVariantTranslations($dto, $productVaraint);

        // update variant images
        if($dto->image_ids){
            ProductVaraintImages::where('variant_id', $productVaraint->id)->delete();
            foreach($dto->image_ids as $imageId){
                ProductVaraintImages::create([
                    'variant_id' => $productVaraint->id,
                    'image_id' => $imageId,
                ]); 
            }
        }

        return $productVaraint;

        
    }




    public function updateVariantTranslations($dto, $productVaraint){
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
            if(isset($dto->meta_title[$locale->code])){
                $productVaraint->translateOrNew($locale->code)->meta_title = $dto->meta_title[$locale->code];
            }
            if(isset($dto->meta_des[$locale->code])){
                $productVaraint->translateOrNew($locale->code)->meta_des = $dto->meta_des[$locale->code];
            }
            $productVaraint->save();
        }
    }

    




}
