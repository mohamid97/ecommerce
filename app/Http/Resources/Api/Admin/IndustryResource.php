<?php

namespace App\Http\Resources\Api\Admin;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndustryResource extends JsonResource
{
    use HandlesImage;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->getColumnLang('slug'),
            'title' => $this->getColumnLang('title'),
            'small_des' => $this->getColumnLang('small_des'),
            'des' => $this->getColumnLang('des'),
            'meta_title' => $this->getColumnLang('meta_title'),
            'meta_des' => $this->getColumnLang('meta_des'),
            'industry_image' => $this->getImageUrl($this->industry_image),
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'title_image' => $this->getColumnLang('title_image'),
            'alt_image' => $this->getColumnLang('alt_image'),
            'order' => $this->order,
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),
            'products' => $this->whenLoaded('products', function () {
                return $this->getColumnsLangWithArrayRelation(['title', 'slug'], 'products', ['product_image']);
            }),
        ];
    }
}
