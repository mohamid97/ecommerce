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
            'bundle_price'=>$this->price,
            'status'=>$this->status,
            'bundle_price'=>$this->getImageUrl($this->bundle_image),
            'category'=>$this->whenLoaded('category', function () {
                $this->getColumnsLangWithArrayRelation(['slug' , 'title'] , 'category' , ['id']);

            }),
            'brand'=>$this->whenLoaded('brand', function () {
                $this->getColumnsLangWithArrayRelation(['slug' , 'title'] , 'brand' , ['id']);
            }),
            'status'=>$this->status,
            'title'=>$this->getColumnLang('title'),
            'created_at'=>$this->created_at->format('Y-m-d'),
            'updated_at'=>$this->updated_at->format('Y-m-d'),


        ];
    }
}
