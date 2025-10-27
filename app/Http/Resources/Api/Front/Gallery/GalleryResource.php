<?php

namespace App\Http\Resources\Api\Front\Gallery;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
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
            'id'=>$this->id,
            'image'=>$this->getImageUrl($this->image),
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
    }
}