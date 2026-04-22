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
            'price'=>(float) $this->getBundlePrice(),
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
                        'product' => [
                            'id'=>$detail->product->id,
                            'title'=>$detail->product->title,
                            'sale_price'=>$detail->product->sale_price,
                            'discount_price'=>$detail->product->discount_price,
                            'discount_type'=>$detail->product->discount_type,
                            'product_image'=>$this->getImageUrl($detail->product->image),
                            'price_after_discount'=>$detail->product->getDiscountPrice(),
                            // 'options'=>$detail->product?->variants->map(function ($varaint) {
                            //     return $varaint->variants->map(function($varaint){
                                 
                            //         return [
                            //             'id' => $varaint->id,
                            //             'title' => $varaint->title,
                            //             'values'=>$varaint->values->map(function ($value) {
                            //                 return [
                            //                     'id' => $value->id,
                            //                     'title' => $value->title,
                            //                 ];
                            //             }),
                            //         ];
                            //     });
       
                            // }),
                            // 'options_available'=>$detail->product->options->map(function ($productOption) {
                            //             return [
                            //                 'option' => $productOption->option ? [
                            //                             'id' => $productOption->id,
                            //                             'title' => $productOption->option->title ?? null,
                            //                 ] : null,

                            //                 'values' => $productOption?->values->map(function ($value) {

                            //                     return [
                            //                         'id' => $value?->optionValue?->id,
                            //                         'title' => $value?->optionValue?->title ?? null,
                            //                     ];

                            //                 }),
                            //             ];
                            // }),

                            
                            'product_varaints' => $detail->getVariants()->map(function ($variant) {
                                //  dd('dsds' , $variant);
                                    return [
                                        'id' => $variant->id,
                                        'title' => $variant->title,
                                        'sale_price'=>$variant->sale_price,
                                        'discount_price'=>$variant->discount_price,
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
