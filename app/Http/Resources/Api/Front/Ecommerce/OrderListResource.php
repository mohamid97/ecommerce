<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number ?? null,
            'status' => $this->status,
            'payment_status' => $this->payment_status ?? 'unpaid',
            'total' => (float) ($this->total_after_discount ?? $this->total),
            'created_at' => $this->created_at?->format('Y-m-d') ?? null,
        ];
    }
}
