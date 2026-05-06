<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use App\Traits\HandlesUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistVariantProductResource extends JsonResource
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
        $variant = $this->variant;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'product' => $product ? [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->getColumnLang('slug'),
                'des' => $product->des,
                'has_options' => (bool) $product->has_options,
                'product_image' => $this->getImageUrl($product->product_image),
                'breadcrumb' => $this->getImageUrl($product->breadcrumb),
                'status' => $product->status,
            ] : null,
            'variant' => $variant ? [
                'id' => $variant->id,
                'title' => $variant->title,
                'sku' => $variant->sku,
                'sale_price' => (float) $variant->sale_price,
                'discount' => (float) $variant->discount_value,
                'discount_type' => $variant->discount_type,
                'price_after_discount' => (float) $variant->getDiscountPrice(),
                'stock' => $variant->stock,
                'status' => $variant->status,
                'is_default' => (bool) $variant->is_default,
            ] : null,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
