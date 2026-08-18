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
        if(isset($this->slug) && is_array($this->slug)){
            $slug = $this->getColumnLang('slug');
        }else{
         $slug = $this->createSlugFromTitle();

        }

        $effectiveDiscount = $this->bundle->getEffectiveDiscount(
            (float) $this->getBundlePrice()['total_price'],
            (float) $this->getBundlePrice()['price_after_discount']
        );

        return [
            'id'=>$this->id,
            'price'=>(float) $this->getBundlePrice()['total_price'],
            'price_after_discount'=>(float) $this->getBundlePrice()['price_after_discount'],
            'discount' => $effectiveDiscount['discount'],
            'discount_type' => $effectiveDiscount['discount_type'],
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
            'slug'=>$slug,
            'des'=>$this->getColumnLang('des'),
            'meta_title'=>$this->getColumnLang('meta_title'),
            'meta_des'=>$this->getColumnLang('meta_des'),
            'bundle_details'=>$this->whenLoaded('bundelDetails', function () {
                return $this->bundelDetails->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'product_id'=>$detail->product->id,
                        'title'=>$detail->product->title,
                        'sale_price'=>(float) $detail->product->sale_price,
                        'status'=>$detail->product->status,
                        'stock'=>$detail->product->stock,
                        'price_after_discount'=>(float) $detail->product->getDiscountPrice(),
                        'discount'=>(float) $detail->product->discount,
                        'discount_type'=> $detail->product->discount_type,
                        'product_image'=>$this->getImageUrl($detail?->product?->product_image),
                    ];
                });
            }),
            'created_at'=>$this->created_at->format('Y-m-d'),
            'updated_at'=>$this->updated_at->format('Y-m-d'),

        ];

       
    }

    protected function createSlugFromTitle(): array
    {
        $data = [];

        $data['ar'] = strtolower((string) preg_replace('/\s+/u', '-', trim($this->translate('ar')->title)));
        $data['en'] = strtolower((string) preg_replace('/\s+/u', '-', trim($this->translate('en')->title)));
        return $data;
    }


    
}