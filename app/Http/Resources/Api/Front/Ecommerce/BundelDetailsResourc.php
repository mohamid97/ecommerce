<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundelDetailsResourc extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'bundle_price'=>$this->price??null,
            'status'=>$this->status,
            'bundle_image'=>$this->getImageUrl($this->bundle_image),
            'category'=>$this->whenLoaded('category', function () {
                $this->getColumnsLangWithArrayRelation(['slug' , 'title'] , 'category' , ['id']);

            }),
            'brand'=>$this->whenLoaded('brand', function () {
                $this->getColumnsLangWithArrayRelation(['slug' , 'title'] , 'brand' , ['id']);
            }),
            'title'=>$this->getColumnLang('title'),
            'des'=>$this->getColumnLang('des'),
            'meta_title'=>$this->getColumnLang('meta_title'),
            'meta_des'=>$this->getColumnLang('meta_des'),
            'bundle_details'=>$this->whenLoaded('bundelDetails', function () {
                return $this->bundelDetails->map(function ($detail) {
                    return [
                        // 'id' => $detail->id,
                        'product' => [
                            'id'=>$detail->product->id,
                            'title'=>$detail->product->title,
                            'sale_price'=>$detail->product->sale_price,
                            'discount_price'=>$detail->product->discount_price,
                            'discount_type'=>$detail->product->discount_type,
                            'price_after_discount'=>$detail->product->getDiscountPrice(),
                            'options'=>$detail->product?->variants?->variants?->map(function ($option) {
                                return [
                                    'id' => $option->id,
                                    'title' => $option->title,
                                    'values'=>$option->values->map(function ($value) {
                                        return [
                                            'id' => $value->id,
                                            'title' => $value->title,
                                        ];
                                    }),
                                ];
                            }),
                            'product_varaints' => $detail->variants->map(function ($variant) {
                                    return [
                                        'id' => $variant->id,
                                        'title' => $variant->title,
                                        'sale_price'=>$variant->sale_price,
                                        'discount_price'=>$variant->discount_price,
                                        'discount_type'=>$variant->discount_type,
                                        'option_values'=>$variant->variants->map(function($variant){
                                            return [
                                                'option_id'=>$variant->optionValue->option?->id,
                                                'value_id'=>$variant->optionValue->id,
                                            ];
                                        }),
                                        'price_after_discount'=>$variant->getDiscountPrice(),
                                    ];
                            }),
                            'product_quantity' => $detail->quantity,

                        ],

                    ];
                });
            }),
            'created_at'=>$this->created_at->format('Y-m-d'),
            'updated_at'=>$this->updated_at->format('Y-m-d'),
            
        ];

       
    }




    protected function buildVariantName($variant)
    {
        return $variant->variants
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
