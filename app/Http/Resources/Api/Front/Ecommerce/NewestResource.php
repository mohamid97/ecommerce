<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use App\Traits\HandlesUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewestResource extends JsonResource
{
    use HandlesUpload;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $selectedVariantId = $this->getSelectedVariantId();
        $variants = $this->getVariants();
        $resolvedVariant = $this->resolveVariant($variants, $selectedVariantId);
        $priceSource = $this->has_options && $resolvedVariant ? $resolvedVariant : $this;

        $minPrice = null;
        $maxPrice = null;
        if ($this->has_options) {
            if ($variants->isNotEmpty()) {
                $minPrice = (float) $variants->min('sale_price');
                $maxPrice = (float) $variants->max('sale_price');
            } else {
                $minPrice = $maxPrice = (float) $this->sale_price;
            }
        } else {
            $minPrice = $maxPrice = (float) $this->sale_price;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->getColumnLang('slug'),
            'sale_price' => (float) $priceSource->sale_price,
            'moq' => $priceSource->moq ?? $this->moq ?? 1,
            'discount_price' => (float) ($priceSource->discount_value ?? $priceSource->discount ?? 0),
            'discount_type' => $priceSource->discount_type,
            'price_after_discount' => (float) $priceSource->getDiscountPrice(),
            'price_min' => $minPrice,
            'price_max' => $maxPrice,
            'on_demand' => $this->on_demand,
            'sku' => $priceSource->sku,
            'has_options' => $this->has_options,
            'product_image' => $this->getImageUrl($this->product_image),
            'status' => $this->has_options ? ($priceSource->status ?? $this->status) : $this->status,
            'stock' => $this->has_options ? ($priceSource->stock ?? $this->stock) : $this->stock,
            'variant_id' => $resolvedVariant?->id,
            'default_varaint' => $this->has_options && $resolvedVariant ? [
                'id' => $resolvedVariant->id,
                'title' => $resolvedVariant->title,
                'sku' => $resolvedVariant->sku,
                'sale_price' => (float) $resolvedVariant->sale_price,
                'discount' => (float) ($resolvedVariant->discount_value ?? $resolvedVariant->discount ?? 0),
                'discount_type' => $resolvedVariant->discount_type,
                'price_after_discount' => (float) $resolvedVariant->getDiscountPrice(),
                'stock' => $resolvedVariant->stock,
                'status' => $resolvedVariant->status,
                'is_default' => (bool) $resolvedVariant->is_default,
                'moq' => $resolvedVariant->moq ?? 1,
            ] : null,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }

    private function getSelectedVariantId(): ?int
    {
        if (!$this->has_options) {
            return null;
        }

        $selectedVariantId = $this->getAttribute('selected_variant_id');

        return is_numeric($selectedVariantId) ? (int) $selectedVariantId : null;
    }

    private function getVariants()
    {
        if (!$this->has_options) {
            return collect();
        }

        return $this->relationLoaded('variants')
            ? $this->variants->where('status', '!=', 'draft')
            : $this->variants()->where('status', '!=', 'draft')->get();
    }

    private function resolveVariant($variants, ?int $selectedVariantId = null)
    {
        if (!$this->has_options || $variants->isEmpty()) {
            return null;
        }

        if ($selectedVariantId !== null) {
            $selectedVariant = $variants->firstWhere('id', $selectedVariantId);
            if ($selectedVariant) {
                return $selectedVariant;
            }
        }

        return $variants->firstWhere('is_default', true)
            ?? $variants->firstWhere('is_default', 1)
            ?? $variants->first();
    }
}
