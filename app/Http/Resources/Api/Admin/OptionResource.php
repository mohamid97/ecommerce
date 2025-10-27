<?php

namespace App\Http\Resources\Api\Admin;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionResource extends JsonResource
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
            'option_image' => $this->getImageUrl($this->option_image),
            'title' => $this->getColumnLang('title'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}