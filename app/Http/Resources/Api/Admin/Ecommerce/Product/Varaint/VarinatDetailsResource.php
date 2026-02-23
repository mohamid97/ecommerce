<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VarinatDetailsResource extends JsonResource
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
            'sku' => $this->sku,
            'sale_price' => $this->sale_price,
            'stock' => $this->stock,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'status' => $this->status,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'weight' => $this->weight,
            'delivery_time' => $this->delivery_time,
            'max_time' => $this->max_time,
            'title'=>$this->getColumnLang('title'),
            'slug'=>$this->getColumnLang('slug'),
            'des'=>$this->getColumnLang('des'),
            'meta_title'=>$this->getColumnLang('meta_title'),
            'meta_des'=>$this->getColumnLang('meta_title'),
            'variant_full_name' => $this->whenLoaded('variants', function () {
                    return $this->buildVariantName();
             }),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }


        protected function buildVariantName()
        {
            return $this->variants
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
