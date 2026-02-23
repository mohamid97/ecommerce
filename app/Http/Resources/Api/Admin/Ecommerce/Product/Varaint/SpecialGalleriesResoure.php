<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialGalleriesResoure extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => $this->whenLoaded('image', function () {
                return $this->getImageUrl($this->image->image);
            }),
        ];
    }
}
