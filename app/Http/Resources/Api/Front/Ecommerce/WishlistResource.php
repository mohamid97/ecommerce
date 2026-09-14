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
        $product = $this->product;
        $resolvedVariant = $this->resolveVariant();
        $priceSource = $resolvedVariant ?? $product;

        $minPrice = null;
        $maxPrice = null;
        if ($product?->has_options) {
            $variants = $product->relationLoaded('variants')
                ? $product->variants->where('status', '!=', 'draft')
                : $product->variants()->where('status', '!=', 'draft')->get();

            if ($variants->isNotEmpty()) {
                $minPrice = (float) $variants->min('sale_price');
                $maxPrice = (float) $variants->max('sale_price');
            } else {
                $minPrice = $maxPrice = (float) $product->sale_price;
            }
        } else {
            $minPrice = $maxPrice = (float) $product?->sale_price;
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'variant_id' => $resolvedVariant?->id,
            'title' => $product?->title,
            'slug' => $product?->slug,
            'moq' => $priceSource->moq ?? $product?->moq ?? 1,
            'sku' => $priceSource->sku,
            'has_options' => (bool) $product?->has_options,
            'product_image' => $this->getImageUrl($product?->product_image),
            'status' => $product?->status,
            'stock' => $resolvedVariant?->stock ?? $product?->stock,
            'default_varaint' => $resolvedVariant ? [
                'id' => $resolvedVariant->id,
                'title' => $resolvedVariant->title,
                'sku' => $resolvedVariant->sku,
                // price and discount removed
                'stock' => $resolvedVariant->stock,
                'status' => $resolvedVariant->status,
                'is_default' => (bool) $resolvedVariant->is_default,
                'moq' => $resolvedVariant->moq ?? 1,
            ] : null,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }

    private function resolveVariant(): mixed
    {
        $product = $this->product;
        if (!$product?->has_options) {
            return null;
        }

        $variants = $product->relationLoaded('variants')
            ? $product->variants->where('status', '!=', 'draft')
            : $product->variants()->where('status', '!=', 'draft')->get();

        if ($variants->isEmpty()) {
            return null;
        }

        if ($this->variant_id) {
            $selectedVariant = $variants->firstWhere('id', $this->variant_id);
            if ($selectedVariant) {
                return $selectedVariant;
            }
        }

        return $variants->firstWhere('is_default', true)
            ?? $variants->firstWhere('is_default', 1)
            ?? $variants->first();
    }
}
