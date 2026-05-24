<?php

namespace App\Http\Resources\Api\Admin;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DynamicfeatureResource extends JsonResource
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
            'icon' => $this->getImageUrl($this->icon),
            'type' => $this->type,
            'title' => $this->getColumnLang('title'),
            'small_des' => $this->getColumnLang('small_des'),
            'des' => $this->getColumnLang('des'),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d') : null,
        ];
    }
}
