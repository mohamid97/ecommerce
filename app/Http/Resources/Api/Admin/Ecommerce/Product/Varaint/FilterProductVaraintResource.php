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
        return [
            'id'=>$this->id,
            'title'=>$this->getColumnLang('title'),
            'slug'=>$this->getColumnLang('slug'),
            'sale_price'=>$this->sale_price,
            'variants'=>[
                $this->whenLoaded('variants', function () {
                                return $this->variants->map(function ($variant) {
                                    return $this->buildVariantName($variant);

                                });
                }),
           ]
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
        ];
    }


}
