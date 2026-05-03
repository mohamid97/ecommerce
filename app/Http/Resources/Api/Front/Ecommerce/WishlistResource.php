<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use App\Traits\HandlesUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    use HandlesUpload;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'product' => $this->product?->title,
            'product_image' => $this->getImageUrl($this->product?->product_image),
            'has_options' => (bool) $this->product?->has_options,
            'variant_id' => $this->variant_id,
            'variant' => $this->variant?->title,
            'varaint_name' => $this->variant_id ? $this->buildVariantName($this->variant) : null,
            'sale_price' => (float) ($this->variant?->sale_price ?? $this->product?->sale_price),
            'price_after_discount' => (float) ($this->variant?->getDiscountPrice() ?? $this->product?->getDiscountPrice()),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }

    protected function buildVariantName($variant)
    {
        return $variant?->variants
            ->map(function ($variantOptionValue) {
                $optionTitle = optional($variantOptionValue->optionValue?->option)->title;
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
