<?php

namespace App\Http\Resources\Api\Admin;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OurworkResource extends JsonResource
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
            'link' => $this->link,
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'ourwork_image' => $this->getImageUrl($this->ourwork_image),
            'type' => $this->type,
            'title' => $this->getColumnLang('title'),
            'slug' => $this->getColumnLang('slug'),
            'location'=>$this->getColumnLang('location'),
            'small_des'=>$this->getColumnLang('small_des'),
            'des' => $this->getColumnLang('des'),
            'meta_title' => $this->getColumnLang('meta_title'),
            'meta_des' => $this->getColumnLang('meta_des'),
            'client' => $this->whenLoaded('client' , function(){
                        return [
                            'id'=>$this->client ? $this->client->id : null,
                            'title'=>$this->client ? $this->client->title : null,
                            'logo'=>$this->client ? $this->getImageUrl($this->client->logo) : null,
                        ];
            }),
            'category' => $this->whenLoaded('category' , function(){
                        return [
                            'id'=>$this->category ? $this->category->id : null,
                            'title'=>$this->category ? $this->category->title : null,
                            'slug' => $this->slug  ? $this->category->slug : null
                        ];
            }),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d') : null,

        ];
    }
}