<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductNoOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->getColumnLang('slug'),
            'des' => $this->des,
            'sale_price' => $this->sale_price,
            'discount_price' => $this->discount_price,
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
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
