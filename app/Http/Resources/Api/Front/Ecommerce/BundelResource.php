<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'status'=>$this->status,
            'bundle_image'=>$this->getImageUrl($this->bundle_image),
            'price'=>(float) $this->getBundlePrice()['total_price'],
            'price_after_discount'=>(float) $this->getBundlePrice()['price_after_discount'],
            'discount' => (float) ($this->discount ?? 0),
            'discount_type' => $this->discount_type,
            'category'=>$this->whenLoaded('category', function () {
                return [
                    'title'=>$this->category->title,
                    'slug'=>$this->category->slug,
                    'id'=>$this->category->id,
                ];
            }),
            'brand'=>$this->whenLoaded('brand', function () {
                return [
                    'title'=>$this->brand->title,
                    'slug'=>$this->brand->slug,
                    'id'=>$this->brand->id,
                ];
            }),
            'title'=>$this->getColumnLang('title'),
            // need to make slug from title if has no slug in translation table 
            'slug'=> $this->getColumnLang('slug') ?? $this->createSlugFromTitle($this->getColumnLang('title')),
            'created_at'=>$this->created_at->format('Y-m-d'),
            'updated_at'=>$this->updated_at->format('Y-m-d'),
        ];
    }

    // need also can validate null value
    protected function createSlugFromTitle(?array $title): string
    {
      
        return (is_array($title)) ? strtolower(str_replace(' ', '-', $title['en'] ?? $title['ar'] ?? '')) : '';
    }
}
