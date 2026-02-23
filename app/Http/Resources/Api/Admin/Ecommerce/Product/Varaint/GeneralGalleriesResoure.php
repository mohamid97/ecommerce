<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralGalleriesResoure extends JsonResource
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
              'image'=>$this->getImageUrl($this->image),
            ];
    }
}
