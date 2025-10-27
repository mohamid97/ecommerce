<?php

namespace App\Http\Resources\Api\Admin;
use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'type'=>$this->type,
            'image' => $this->getImageUrl($this->image),
            'title' => $this->getColumnLang('title'),
            'des' => $this->getColumnLang('des'),
            'alt_image' => $this->getColumnLang('alt_image'),
            'title_image' => $this->getColumnLang('title_image'),
            'created_at' => $this->created_at->format('Y-m-d '),
            'updated_at' => $this->updated_at->format('Y-m-d '),
        ];
    }
}