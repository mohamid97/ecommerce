<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use App\Models\Api\Ecommerce\ProductVariant;
use App\Traits\HandlesUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionProductResource extends JsonResource
{
    use HandlesUpload;

    public function toArray(Request $request): array
    {
        $product = $this->product;
        $variant = $this->resolveVariant($product);
        $priceSource = $variant ?? $product;
        $variantImage = $variant?->varaintImages?->first()?->image?->image;

        return [
            // This ID makes repeated product rows unique in newest/last-piece.
            'section_item_id' => $this->id,
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->getColumnLang('slug'),
            'sale_price' => (float) $priceSource->sale_price,
            'moq' => $priceSource->moq ?? $product->moq ?? 1,
            'discount_price' => (float) ($priceSource->discount_value ?? $priceSource->discount ?? 0),
            'discount_type' => $priceSource->discount_type,
            'price_after_discount' => (float) $priceSource->getDiscountPrice(),
            'on_demand' => $product->on_demand,
            'sku' => $priceSource->sku,
            'has_options' => (bool) $product->has_options,
            'product_image' => $this->getImageUrl($variantImage ?: $product->product_image),
            'variant_image' => $this->getImageUrl($variantImage),
            'status' => $variant?->status ?? $product->status,
            'stock' => $variant?->stock ?? $product->stock,
            'variant_id' => $variant?->id,
            'variant_name' => $variant?->getVariantFullNameAttribute(),
            'created_at' => $product->created_at?->format('Y-m-d'),
            'updated_at' => $product->updated_at?->format('Y-m-d'),
        ];
    }

    private function resolveVariant($product): ?ProductVariant
    {
        if (!$product?->has_options) {
            return null;
        }

        if ($this->variant && $this->variant->status !== 'draft') {
            return $this->variant;
        }

        $variants = $product->relationLoaded('variants')
            ? $product->variants->where('status', '!=', 'draft')
            : $product->variants()->where('status', '!=', 'draft')->get();

        return $variants->firstWhere('is_default', true)
            ?? $variants->firstWhere('is_default', 1)
            ?? $variants->first();
    }
}
