<?php

namespace App\Http\Resources\Api\Admin\Bundel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundeDetailsResource extends JsonResource
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
            'price'=>$this->price,
            'category_id'=>$this->category_id,
            'brand_id'=>$this->brand_id,
            'bundle_image'=>$this->bundle_image,
            'status'=>$this->status,
            'title'=>$this->getColumnLang('title'),
            'des'=>$this->getColumnLang('des'),
            'meta_title'=>$this->getColumnLang('meta_title'),
            'meta_des'=>$this->getColumnLang('meta_des'),
            ''
            
        ];
    }
}
