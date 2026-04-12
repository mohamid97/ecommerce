<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint\ProductVaraintsResource;
use App\Http\Resources\Api\Admin\OptionResource;
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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug'=>$this->getColumnLang('slug'),
            'des' => $this->des,
            'sale_price' => $this->sale_price,
            'discount_price' => $this->discount_price,
            'discount_type' => $this->discount_type,
            'on_demand' => $this->on_demand,
            'sku' => $this->sku,
            'has_options' => $this->has_options,
            'product_image' => $this->getImageUrl($this->product_image),
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'status'=>$this->status,
            'stock'=>$this->stock,
            'category' => $this->category?->title,
            'brand'    => $this->brand?->title,
            'options' => $this->whenLoaded('options', function () {

                return $this->options?->map(function ($productOption) {

                    return [
                        'option' => $productOption->option ? [
                           'id' => $productOption->id,
                            'title' => $productOption->option->title ?? null,
                        ] : null,

                        'values' => $productOption->values->map(function ($value) {

                            return [
                                'id' => $value->id,
                                'title' => $value->optionValue->title ?? null,
                            ];

                        }),
                    ];

                });

            }),
            // need to get option_ids and varaint collect name
            'varaints'=>CustomProductVaraintsResource::collection($this->variants),
            'default_varaint' =>($this->variants->where('is_default', 1)->first()) ? $this->productDefaultVariant($this->variants->where('is_default', 1)->first()) : null,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }




    protected function productDefaultVariant($varaints){

       
        
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'sale_price' =>  (float)$this->sale_price,
            'stock' => $this->stock,
            'discount' => (float) $this->discount,
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
            'variant_full_name' => $this->buildVariantName($varaints->variants),
            'option_ids'=>$varaints->variants->pluck('option_value_id'),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
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
