<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Variant;

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
    }
}