<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Variant;

use App\Models\Api\Admin\Lang;
use App\Models\Api\Ecommerce\ProductVariant;

class UpdateVaraintAction
{
    public function updateVariant($dto){
        ProductVariant::where('id', $dto->variant_id)->update([
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
        $productVaraint = ProductVariant::find($dto->variant_id);
        $this->updateVariantTranslations($dto, $productVaraint);

        
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