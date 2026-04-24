<?php

namespace App\Http\Resources\Api\Admin\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointsSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'min_order_amount' => (float) $this->min_order_amount,
            'points' => (int) $this->points,
            'pound_per_point' => (float) $this->pound_per_point,
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),
        ];
    }
}
