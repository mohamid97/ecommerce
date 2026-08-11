<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentWayZoneResource extends JsonResource
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
            'price' => (float) $this->price,
            'status' => $this->status,
            'way' => $this->whenLoaded('way', function () {
                return $this->way ? [
                    'id' => $this->way->id,
                    'title' => $this->way->title,
                    'capacity' => (int) $this->way->capacity,
                ] : null;
            }),
            'zone' => $this->whenLoaded('zone', function () {
                return $this->zone ? [
                    'id' => $this->zone->id,
                    'title' => $this->zone->title,
                ] : null;
            }),
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),
        ];
    }
}