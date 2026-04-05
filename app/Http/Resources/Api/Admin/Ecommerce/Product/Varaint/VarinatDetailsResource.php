<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VarinatDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'sale_price' =>  (float)$this->sale_price,
            'stock' => $this->stock,
            'discount' => (float) $this->discount,
            'discount_type' => $this->discount_type,

            'status' => $this->status,
            'product'=>$this->whenLoaded('product' , function(){
                return [
                    'id'=>$this->product->id,
                    'title'=>$this->product->title,
                    'on_demand'=>$this->product->on_demand,
                    'sale_price'=>(float)$this->product->sale_price,
                ];
            }),
            'shipmentDetails'=>[
                'length' => (float)$this->length,
                'width' => (float)$this->width,
                'height' => (float)$this->height,
                'weight' => (float)$this->weight,
                'min_estimated_delivery' => $this->delivery_time,  
                'max_estimated_delivery' => $this->max_time,
            ],
            'title'=>$this->getColumnLang('title'),
            'slug'=>$this->getColumnLang('slug'),
            'des'=>$this->getColumnLang('des'),
            'meta_title'=>$this->getColumnLang('meta_title'),
            'meta_des'=>$this->getColumnLang('meta_des'),
            'varaint_images'=>$this->whenLoaded('varaintImages', function () {
                return $this->varaintImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image' => $this->getImageUrl($image->image?->image)
                    ];
                });
            }),
            'variant_full_name' => $this->whenLoaded('variants', function () {
                    return $this->buildVariantName();
             }),
             'is_default'=>(bool) $this->isDefault,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }


    protected function buildVariantName()
    {
        return $this->variants
            ->map(function ($variantOptionValue) {

                $optionTitle = optional(
                    $variantOptionValue->optionValue?->option
                )->title;

                $valueTitle = $variantOptionValue->optionValue?->title;

                if (!$optionTitle || !$valueTitle) {
                    return null;
                }

                return $optionTitle . ' ' . $valueTitle;

            })
            ->filter()
            ->implode(' ');
    }




}
