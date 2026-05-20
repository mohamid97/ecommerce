<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilterProductVaraintResource extends JsonResource
{

    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = isset($this->has_options) ? 'product' : 'bundle';
    
        return [
            'id'=>$this->id,
            'title'=>$this->getColumnLang('title'),
            'slug'=>$this->getColumnLang('slug'),
            'type'=>$type,
            'sale_price'=>($type) == 'product' ? (float)$this->sale_price : $this->getBundlePrice()['total_price'],
            'price_after_discount'=> ($type) == 'product' ? $this->getDiscountPrice() : $this->getBundlePrice()['price_after_discount'],
            'product'=>$this->whenLoaded('bundelDetails', function () {
                        return $this->bundelDetails->map(function ($detail) {
                            $product = $detail->product;
                            return [
                                'id'=>$product->id,
                                'title'=>$product->title,
                                'slug'=>$product->slug,
                                'varaint'=>$detail->getVariants()->map(function ($variant) {
                                    return [
                                        'id'=>$variant->id,
                                        'combinations'=>$variant->variants
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
                                        ->implode(' '),
                                        'sale_price'=>$variant->sale_price,
                                        'price_after_discount'=>$variant->getDiscountPrice(),
                                    ];
                                }),
                            ];
                        });
                    }),

             // need this to show only if type product
            'variants' => $this->when(
                $type === 'product',
                function () {
                    return $this->whenLoaded('variants', function () {
                        return $this->variants->map(fn ($variant) => $this->buildVariantName($variant));
                    });
                }
            ),
           
        ];
    }







    protected function buildVariantName($variant)
    {
        
        return [
            'id'=>$variant->id,
            'combinations'=>$variant->variants
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
            ->implode(' '),
            'sale_price'=>$variant->sale_price,
            'price_after_discount'=>$variant->getDiscountPrice(),

        ];
    }


}
