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
        return[
            'id' => $this->id,
            'title' => $this->title,
            'slug'=>$this->getColumnLang('slug'),
            'des' => $this->des,
            'sale_price' => $this->sale_price,
            'discount_price' =>(float) $this->discount,
            'discount_type' => $this->discount_type,
            'on_demand' => $this->on_demand,
            'sku' => $this->sku,
            'has_options' => $this->has_options,
            'product_image' => $this->getImageUrl($this->product_image),
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'status'=>$this->status,
            'stock'=>$this->stock,
            'category' => $this->category?->title,
            'brand'    => $this->brand?->title,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
