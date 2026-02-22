<?php

namespace App\Http\Resources\Api\Admin;

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
            'title' => $this->getColumnLang('title'),
            'slug' => $this->getColumnLang('slug'),
            'cost_price' => $this->cost_price,
            'sales_price' => $this->sales_price,  
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'stock' => $this->stock,
            'category' => $this->whenLoaded('category' , function(){
                    return [
                        'id'=>$this->category ? $this->category->id : null,
                        'title'=>$this->getColumnLang('title','category'),
                        'slug' => $this->getColumnLang('slug','category')
                    ];
            }),

            'brand' => $this->whenLoaded('brand' , function(){
                    return [
                        'id'=>$this->brand ? $this->brand->id : null,
                        'title'=>$this->getColumnLang('title','brand'),
                        'slug'=>$this->getColumnLang('slug','brand'),
                    ];
            }),
            'order' => $this->order,
            'small_des' => $this->getColumnLang('small_des'),
            'des' => $this->getColumnLang('des'),
            'image' => $this->getImageUrl($this->product_image),
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'title_image' => $this->getColumnLang('title_image'),
            'alt_image' => $this->getColumnLang('alt_image'),
            'meta_title' => $this->getColumnLang('meta_title'),
            'meta_des' => $this->getColumnLang('meta_des'),
            'order'=>$this->order,
            'has_options'=>$this->has_options ? true :false,
            'status'=>$this->status,
            'on_demand'=>$this->on_demand ? true :false,
            'options' => $this->whenLoaded('options', function () {

                return $this->options->map(function ($productOption) {

                    return [
                        'id' => $productOption->id,

                        'option' => $productOption->option ? [
                            'id'    => $productOption->option->id,
                            'title' => $productOption->option->title ?? null,
                        ] : null,

                        'values' => $productOption->values->map(function ($value) {

                            return [
                                'id' => $value->id,

                                'option_value' => $value->optionValue ? [
                                    'id'    => $value->optionValue->id,
                                    'title' => $value->optionValue->title ?? null,
                                ] : null,
                            ];

                        }),
                    ];

                });

            }),
          
            'shipmentDetails'=>$this->whenLoaded('shipmentDetails' , function(){
                    return [
                        'width'=>$this->shipmentDetails->width,
                        'height'=>$this->shipmentDetails->height,
                        'length'=>$this->shipmentDetails->length,
                        'weight'=>$this->shipmentDetails->weight,
                        'min_estimated_delivery'=>$this->shipmentDetails->min_estimated_delivery,
                        'max_estimated_delivery'=>$this->shipmentDetails->max_estimated_delivery,
                    ];
            }),

            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),
        
        ];

    }
}
