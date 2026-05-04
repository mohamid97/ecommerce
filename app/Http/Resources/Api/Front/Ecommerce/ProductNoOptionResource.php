<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use App\Traits\HandlesUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductNoOptionResource extends JsonResource
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
            'title' => $this->getColumnLang('title'),
            'slug' => $this->getColumnLang('slug'),
            'meta_title' => $this->getColumnLang('meta_title'),
            'meta_des' => $this->getColumnLang('meta_des'),
            'des' =>$this->getColumnLang('des'),
            'sale_price' => (float) $this->sale_price,
            'price_after_discount'=>(float) $this->getDiscountPrice(),
            'discount' => (float) $this->discount,
            'discount_type' => $this->discount_type,
    
            'on_demand' => $this->on_demand,
            'sku' => $this->sku,
            'has_options' => false,
            'product_image' => $this->getImageUrl($this->product_image),
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'status' => $this->status,
            'stock' => $this->stock,
            'category' => $this->category?->title,
            'brand' => $this->brand?->title,
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
}
