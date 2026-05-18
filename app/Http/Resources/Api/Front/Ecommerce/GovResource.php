<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class GovResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => ['ar' => $this->name_ar, 'en' => $this->name_en],
        ];
    }
}
