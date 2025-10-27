<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\HandlesImage;

class MetasettingResource extends JsonResource
{
    use HandlesImage;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
                  
            "$this->name" =>[
                'id' => $this->id,
                'banner' => $this->getImageUrl($this->banner),
                'meta_title' => $this->getColumnLang('meta_title'),
                'meta_des' => $this->getColumnLang('meta_des'),
                'created_at' => $this->created_at->format('Y-m-d'),
                'updated_at' => $this->updated_at->format('Y-m-d'),
            ], 

            
        ];
    }
}