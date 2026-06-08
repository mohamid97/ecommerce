<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use App\Traits\HandlesUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    use HandlesUpload;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultVaraint = $this->getDefaultVaraint();
        $priceSource = $this->has_options && $defaultVaraint ? $defaultVaraint : $this;

        return[
            'id' => $this->id,
            'title' => $this->title,
            'slug'=>$this->getColumnLang('slug'),
            'des' => $this->des,
            'sale_price' => (float) $priceSource->sale_price,
            'discount_price' => (float) ($defaultVaraint ? $defaultVaraint->discount_value : $this->discount),
            'discount_type' => $priceSource->discount_type,
            'price_after_discount' => (float) $priceSource->getDiscountPrice(),
            'on_demand' => $this->on_demand,
            'sku' => $priceSource->sku,
            'has_options' => $this->has_options,
            'product_image' => $this->getImageUrl($this->product_image),
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'status' => $this->has_options && $defaultVaraint ? $defaultVaraint->status : $this->status,
            'stock' => $this->has_options && $defaultVaraint ? $defaultVaraint->stock : $this->stock,
            'default_varaint' => $this->has_options && $defaultVaraint ? [
                'id' => $defaultVaraint->id,
                'title' => $defaultVaraint->title,
                'sku' => $defaultVaraint->sku,
                'sale_price' => (float) $defaultVaraint->sale_price,
                'discount' => (float) $defaultVaraint->discount_value,
                'discount_type' => $defaultVaraint->discount_type,
                'price_after_discount' => (float) $defaultVaraint->getDiscountPrice(),
                'stock' => $defaultVaraint->stock,
                'status' => $defaultVaraint->status,
                'is_default' => (bool) $defaultVaraint->is_default,
            ] : null,
            // return slug also and id
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'title' => $this->category->title,
                    'slug' => $this->category->slug,
                ];
            }),
            'brand'    => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'title' => $this->brand->title,
                    'slug' => $this->brand->slug,
                ];
            }),
            'industries' => $this->whenLoaded('industries', function () {
                return $this->industries->map(function ($industry) {
                    return [
                        'id' => $industry->id,
                        'title' => $industry->title,
                        'slug' => $industry->slug,
                    ];
                });
            }),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }

    private function getDefaultVaraint()
    {
        if (!$this->has_options) {
            return null;
        }

        $variants = $this->relationLoaded('variants')
            ? $this->variants
            : $this->variants()->where('status', '!=', 'draft')->orderByDesc('is_default')->orderBy('id')->get();

        return $variants->firstWhere('is_default', true)
            ?? $variants->firstWhere('is_default', 1)
            ?? $variants->first();
    }
}
