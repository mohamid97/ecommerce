<?php

namespace App\Http\Resources\Api\Admin;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Testing\Fluent\Concerns\Has;


class SliderResource extends JsonResource
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
            'des' => $this->getColumnLang('des'),
            // 'image'=>$this->image,
            'path' => $this->getImageUrl($this->image),
            'title_image' => $this->getColumnLang('title_image'),
            'alt_image' => $this->getColumnLang('alt_image'),
            'small_des' => $this->getColumnLang('small_des'),
            'order' => $this->order,
            'video' => $this->video,
            'link' => $this->link,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
            
        ];
    }
}