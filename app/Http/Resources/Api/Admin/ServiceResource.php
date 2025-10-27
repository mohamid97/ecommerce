<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\HandlesImage;

class ServiceResource extends JsonResource
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
            'title' => $this->getColumnLang('title'),
            'slug' => $this->getColumnLang('slug'),
            'price' => $this->price,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category' , function(){
                return [
                    'id'=>$this->category ? $this->category->id : null,
                    'title'=>$this->category ? $this->category->title : null,
                    'slug'=>$this->category ? $this->category->slug : null,
                ];
            }),
            'order' => $this->order,
            'small_des' => $this->getColumnLang('small_des'),
            'des' => $this->getColumnLang('des'),
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'service_image' => $this->getImageUrl($this->service_image),
            'title_image' => $this->getColumnLang('title_image'),
            'alt_image' => $this->getColumnLang('alt_image'),
            'meta_title' => $this->getColumnLang('meta_title'),
            'meta_des' => $this->getColumnLang('meta_des'),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}