<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultVaraintModel = $this->variants->firstWhere('is_default', 1) ?? $this->variants->first();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug'=>$this->getColumnLang('slug'),
            'des' => $this->des,
            'sale_price' => $defaultVaraintModel?->sale_price ?? $this->sale_price,
            'discount_price' => $defaultVaraintModel?->discount_value ?? $this->discount_price,
            'discount_type' => $defaultVaraintModel?->discount_type ?? $this->discount_type,
            'on_demand' => $this->on_demand,
            'sku' => $defaultVaraintModel?->sku ?? $this->sku,
            'has_options' => $this->has_options,
            'product_image' => $this->getImageUrl($this->product_image),
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'status' => $defaultVaraintModel?->status ?? $this->status,
            'stock' => $defaultVaraintModel?->stock ?? $this->stock,
            'category' => $this->category?->title,
            'brand'    => $this->brand?->title,
            'options' => $this->whenLoaded('options', function () {

                return $this->options?->map(function ($productOption) {

                    return [
                        'option' => $productOption->option ? [
                           'id' => $productOption->id,
                            'title' => $productOption->option->title ?? null,
                        ] : null,

                        'values' => $productOption?->values->map(function ($value) {

                            return [
                                'id' => $value?->optionValue?->id,
                                'title' => $value?->optionValue?->title ?? null,
                            ];

                        }),
                    ];

                });

            }),
            'varaints' => CustomProductVaraintsResource::collection($this->variants),
            'default_varaint' => $defaultVaraintModel ? $this->productDefaultVariant($defaultVaraintModel) : null,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }




    protected function productDefaultVariant($varaints){

       
        
        return [
            'id' => $varaints->id,
            'sku' => $varaints->sku,
            'sale_price' =>  (float)$varaints->sale_price,
            'stock' => $varaints->stock,
            'discount' => (float) $varaints->discount_value,
            'discount_type' => $varaints->discount_type,
            'status' => $varaints->status,
            'shipmentDetails'=>[
                'length' => (float)$varaints->length,
                'width' => (float)$varaints->width,
                'height' => (float)$varaints->height,
                'weight' => (float)$varaints->weight,
                'min_estimated_delivery' => $varaints->delivery_time,  
                'max_estimated_delivery' => $varaints->max_time,
            ],

            'varaint_images' => $varaints->relationLoaded('varaintImages')
                ? $varaints->varaintImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image' => $this->getImageUrl($image->image?->image)
                    ];
                })
                : [],
            'variant_full_name' => $this->buildVariantName($varaints->variants),
            'option_values' => $varaints->variants->map(function ($variantOptionValue) {
                return [
                    'option_id' => $variantOptionValue->optionValue?->option?->id,
                    'option_value_id' => $variantOptionValue->option_value_id,
                ];
            }),
            'option_ids' => $varaints->variants->pluck('option_value_id'),
            'created_at' => $varaints->created_at->format('Y-m-d'),
            'updated_at' => $varaints->updated_at->format('Y-m-d'),
        ];
    }




    protected function buildVariantName($variants)
    {
        return $variants
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
