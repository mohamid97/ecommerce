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
        ];
    }
}