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
            'price'=>(float) $this->getBundlePrice()['total_price'],
            'price_after_discount'=>(float) $this->getBundlePrice()['price_after_discount'],
            'status'=>$this->status,
            'bundle_image'=>$this->getImageUrl($this->bundle_image),
            'category'=>$this->whenLoaded('category', function () {
               return [
                'title'=>$this->category->title,
                'slug'=>$this->category->slug,
                'id'=>$this->category->id,
               ];

            }),
            'brand'=>$this->whenLoaded('brand', function () {
                return [
                    'title'=>$this->brand->title,
                    'slug'=>$this->brand->slug,
                    'id'=>$this->brand->id,
                ];
            }),
            'title'=>$this->getColumnLang('title'),
            'des'=>$this->getColumnLang('des'),
            'meta_title'=>$this->getColumnLang('meta_title'),
            'meta_des'=>$this->getColumnLang('meta_des'),
            'bundle_details'=>$this->whenLoaded('bundelDetails', function () {
                // dd($this->bundelDetails[1]->product?->variants[1]->variants);
                return $this->bundelDetails->map(function ($detail) {
                    // dd($detail->getVariants());
                    return [
                        
                        // 'id' => $detail->id,

                            'id'=>$detail->product->id,
                            'title'=>$detail->product->title,
                            'sale_price'=>(float) $detail->product->sale_price,
                            'price_afte_discount'=>(float) $detail->product->getDiscountPrice(),
                            'discount'=>(float) $detail->product->discount,
                            'discount_type'=> $detail->product->discount_type,
                            'product_image'=>$this->getImageUrl($detail->product->image),
                            'options' => (function () use ($detail) {
                                $pairs = $detail->product?->variants
                                    ->flatMap(function ($variant) {
                                        return $variant->variants->map(function ($variantOptionValue) {
                                            return [
                                                'option_id' => $variantOptionValue->optionValue->option?->id,
                                                'value_id' => $variantOptionValue->optionValue->id,
                                                'option_title' => $variantOptionValue->optionValue->option?->title,
                                                'value_title' => $variantOptionValue->optionValue->title,
                                            ];
                                        });
                                    })
                                    ->filter()
                                    ->unique(function ($item) {
                                        return ($item['option_id'] ?? '') . '_' . ($item['value_id'] ?? '');
                                    })
                                    ->values();

                                $allVariantValueIds = $detail->getVariants()
                                    ->flatMap(function ($v) {
                                        return $v->variants->map(fn($vv) => $vv->optionValue->id);
                                    })->unique()->values()->toArray();

                                return $pairs->groupBy('option_id')->map(function ($group) use ($allVariantValueIds) {
                                    $first = $group->first();
                                    return [
                                        'option_id' => $first['option_id'],
                                        'option_title' => $first['option_title'] ?? null,
                                        'values' => $group->map(function ($item) use ($allVariantValueIds) {
                                            return [
                                                'value_id' => $item['value_id'],
                                                'value_title' => $item['value_title'] ?? null,
                                                // 'selected' => in_array($item['value_id'], $allVariantValueIds, true),
                                            ];
                                        })->values(),
                                    ];
                                })->values();
                            })(),
                            'product_varaints' => $detail->getVariants()->map(function ($variant) {
                                //  dd('dsds' , $variant);
                                    return [
                                        'id' => $variant->id,
                                        'title' => $variant->title,
                                        'sale_price'=>$variant->sale_price,
                                        'price_after_discount'=>$variant->getDiscountPrice(),
                                        'discount_type'=>$variant->discount_type,
                                        // 'image'=>$this->getImageUrl($variant->image),
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
