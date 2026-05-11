<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->customerName(),
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'type' => $this->type,
            'points' => (int) ($this->points ?? 0),
            'profile_completed' => (bool) ($this->profile?->government && $this->profile?->address),
            'government' => $this->profile?->government,
            'address' => $this->profile?->address,
            'orders_count' => (int) ($this->orders_count ?? 0),
            'total_spent' => (float) ($this->total_spent ?? 0),
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }

    protected function customerName(): ?string
    {
        $fullName = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));

        return $this->name ?: ($fullName ?: null);
    }
}
