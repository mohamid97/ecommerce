<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Bundel;

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
            'price'=>(float) $this->getBundlePrice(),
            'status'=>$this->status,
            'bundle_image'=>$this->getImageUrl($this->bundle_image),
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
            'created_at'=>$this->created_at->format('Y-m-d'),
            'updated_at'=>$this->updated_at->format('Y-m-d'),


        ];
    }
}
