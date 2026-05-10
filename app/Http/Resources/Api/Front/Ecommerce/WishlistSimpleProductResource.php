<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use App\Traits\HandlesUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistSimpleProductResource extends JsonResource
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

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'variant_id' => null,
            'product' => $product ? [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'des' => $product->des,
                'sale_price' => (float) $product->sale_price,
                'discount' => (float) $product->discount,
                'discount_type' => $product->discount_type,
                'price_after_discount' => (float) $product->getDiscountPrice(),
                'sku' => $product->sku,
                'has_options' => (bool) $product->has_options,
                'status' => $product->status,
                'stock' => $product->stock,
                'product_image' => $this->getImageUrl($product->product_image),
                'breadcrumb' => $this->getImageUrl($product->breadcrumb),
            ] : null,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
