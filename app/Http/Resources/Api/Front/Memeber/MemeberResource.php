<?php

namespace App\Http\Resources\Api\Front\Memeber;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemeberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'username'=>$this->username,
            'email'=>$this->email,
            'phone'=>$this->phone,
            'points'=>$this->points,
            'profile_completed'=> (bool) ($this->profile?->city_id && $this->profile?->address),
            'profile'=> [
                'city' => $this->profile?->city?->translate(app()->getLocale())->title ?? $this->profile?->city?->title ?? null,
                'zone' => $this->profile?->zone?->translate(app()->getLocale())->title ?? $this->profile?->zone?->title ?? null,
                'address' => $this->profile?->address,
                'area' => $this->profile?->area,
                'building_number' => $this->profile?->building_number,
                'floor' => $this->profile?->floor,
                'apartment_number' => $this->profile?->apartment_number,
                'landmark' => $this->profile?->landmark,
                'notes' => $this->profile?->notes,
            ],
        ];
    }
}
