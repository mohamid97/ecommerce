<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Bundel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundeDetailsResource extends JsonResource
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
            'image'=>$this->getImageUrl($this->bundle_image),
            'category'=>$this->whenLoaded('category', function () {
                return [
                    'id'=>$this->category->id,
                    'title'=>$this->category->title,
                    'slug'=>$this->category->slug,
                    
                ];

            }),
            'brand'=>$this->whenLoaded('brand', function () {
                return [
                    'id'=>$this->brand->id,
                    'title'=>$this->brand->title,
                    'slug'=>$this->brand->slug,
                    
                ];
            }),
            'status'=>$this->status,
            'title'=>$this->getColumnLang('title'),
            'slug'=>$this->getColumnLang('slug'),
            'des'=>$this->getColumnLang('des'),
            'meta_title'=>$this->getColumnLang('meta_title'),
            'meta_des'=>$this->getColumnLang('meta_des'),
            'bundle_details'=>$this->whenLoaded('bundelDetails', function () {
                return $this->bundelDetails->map(function ($detail) {
                    return [
                        // 'id' => $detail->id,
                        'product' => [
                            'id'=>$detail->product->id  ,
                            'title'=>$detail->product->title,
                            'product_varaints' => $detail->getVariants()->map(function ($variant) {
                                    return [
                                        'id' => $variant->id,
                                        'title' => $variant->title,
                                        'full_name'=>$this->buildVariantName($variant)
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
