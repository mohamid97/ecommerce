<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

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
        $defaultVaraintModel = $this->variants->firstWhere('is_default', 1) ?? $this->variants->first();

        return [

            'id' => $this->id,
            'title' => $this->title,
            'slug'=>$this->getColumnLang('slug'),
            'des' => $this->des,
            'sale_price' => (float) $defaultVaraintModel?->sale_price,
            'price_after_discount' => (float) $defaultVaraintModel?->getDiscountPrice(),
            'discount' =>  (float) $defaultVaraintModel?->discount_value,
            'discount_type' => $defaultVaraintModel?->discount_type,
            'on_demand' => $this->on_demand,
            'sku' => $defaultVaraintModel?->sku,
            'has_options' => $this->has_options,
            'product_image' => $this->getImageUrl($this->product_image),
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'status' => $defaultVaraintModel?->status,
            'stock' => (float) $defaultVaraintModel?->stock,
            'category' => $this->category?->title,
            'brand'    => $this->brand?->title,
            'varaint_images'=> $defaultVaraintModel->varaintImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => $this->getImageUrl($image->image?->image)
                ];
            }),
            'options' => $this->whenLoaded('options', function () {
                return $this->options?->map(function ($productOption) {
                    return [
                        'option' => $productOption->option ? [
                           'id' => $productOption->id,
                            'title' => $productOption->option->title ?? null,
                        ] : null,
                        'values' => $productOption?->values->map(function ($value) {
                            return [
                                'id' => $value?->optionValue?->id,
                                'title' => $value?->optionValue?->title ?? null,
                            ];
                        }),
                    ];
                });
            }),
            'varaints' => CustomProductVaraintsResource::collection($this->variants),
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
