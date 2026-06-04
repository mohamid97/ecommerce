<?php

namespace App\Http\Resources\Api\Front\Memeber;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginMemeberResource extends JsonResource
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
            'email'=>$this->email,
            'username'=>$this->username,
            'phone'=>$this->phone,
            'profile_completed'=> (bool) ($this->profile?->government && $this->profile?->address),
            'profile'=> [
                'government' => ['ar'=>$this->profile?->government?->name_ar,'en'=>$this->profile?->government?->name_en],
                'address' => $this->profile?->address,
                'city' => $this->profile?->city,
                'area' => $this->profile?->area,
                'building_number' => $this->profile?->building_number,
                'floor' => $this->profile?->floor,
                'apartment_number' => $this->profile?->apartment_number,
                'landmark' => $this->profile?->landmark,
                'notes' => $this->profile?->notes,
            ],
            'token'=>$this->token
        ];
    }
}
