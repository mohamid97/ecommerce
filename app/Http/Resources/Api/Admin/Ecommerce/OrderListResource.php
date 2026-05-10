<?php

namespace App\Http\Resources\Api\Admin\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number ?? null,
            'customer' => [
                'id' => $this->user_id,
                'name' => $this->customerName(),
                'email' => $this->customerEmail(),
                'type' => $this->user_id ? 'user' : 'guest',
            ],
            'status' => $this->status,
            'payment_status' => $this->payment_status ?? 'unpaid',
            'payment_method' => $this->payment_method,
            'total' => (float) ($this->total_after_discount ?? $this->total),
            'points_earned' => (int) ($this->points_earned ?? 0),
            'items_count' => $this->items_count ?? null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function customerName(): ?string
    {
        if ($this->user) {
            $fullName = trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? ''));

            return $this->user->name ?: ($fullName ?: null);
        }

        return $this->guest_name;
    }

    private function customerEmail(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }
}
