<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Product;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSectionResource extends JsonResource
{
    use HandlesImage;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->product;

        return [
            'id' => $product?->id,
            'title' => $this->getColumnLang('title', 'product'),
            'slug' => $this->getColumnLang('slug', 'product'),
            'image' => $this->getImageUrl($product?->product_image),
            'variant' => $this->variantData(),
        ];
    }

    private function variantData(): array
    {
        $variant = $this->variant;
        $variantImage = $variant?->varaintImages?->first()?->image?->image;

        return [
            'id' => $variant?->id,
            'combination_name' => $variant ? $this->buildVariantName($variant) : null,
            'image' => $this->getImageUrl($variantImage ?: $this->product?->product_image),
        ];
    }

    private function buildVariantName($variant): string
    {
        return $variant->variants
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
