<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VaraintDetailsResource extends JsonResource
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
            'price_after_discount' => (float)$this->getDiscountPrice(),
            'stock' => $this->stock,
            'discount' => (float) $this->discount_value,
            'discount_type' => $this->discount_type,
            'status' => $this->status,
            'shipmentDetails'=>[
                'length' => (float)$this->length,
                'width' => (float)$this->width,
                'height' => (float)$this->height,
                'weight' => (float)$this->weight,
                'min_estimated_delivery' => $this->delivery_time,  
                'max_estimated_delivery' => $this->max_time,
            ],
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
             'is_default'=>(bool) $this->is_default,
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
