<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'location' => $this->location,
            'address' => $this->getColumnLang('address'),
            'phones' => $this->phones->pluck('phone')->toArray(),
            'emails' => $this->emails->pluck('email')->toArray(),
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),
            'government' => $this->getColumnLang('government'),
            'country' => $this->getColumnLang('country'),
        ];
    }
    

    
}