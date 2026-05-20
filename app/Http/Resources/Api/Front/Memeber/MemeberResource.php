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
            'profile_completed'=> (bool) ($this->profile?->government && $this->profile?->address),
            'profile'=> [
                'government' => ['name_ar' => $this->profile?->government?->name_ar , 'en_name' => $this->profile?->government?->en_name],
                'address' => $this->profile?->address,
                'city' => $this->profile?->city,
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
