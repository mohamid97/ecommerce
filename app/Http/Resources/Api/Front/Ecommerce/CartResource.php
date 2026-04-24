<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'user_id' => $this->user_id,
            'status' => $this->status,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'total_before_discount' => (float) $this->items->sum('total_before_discount'),
            'total_after_discount' => (float) $this->items->sum('total_after_discount'),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
