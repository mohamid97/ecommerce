<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\HandlesImage;

class OurteamResource extends JsonResource
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
            'name' => $this->getColumnLang('name'),
            'image' => $this->getImageUrl($this->image),
            'social'=>[
                'facebook' => $this->facebook,
                'twitter' => $this->twitter,
                'linkedin' => $this->linkedin,
                'instagram' => $this->instagram,
                'youtube' => $this->youtube,
                'tiktok' => $this->tiktok,
            ],
            'position' => $this->getColumnLang('position'),
            'experience' => $this->getColumnLang('experience'),
            'des' => $this->getColumnLang('des'),
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),
        ];
    }
}