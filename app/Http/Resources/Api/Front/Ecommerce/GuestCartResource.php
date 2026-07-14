<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuestCartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Guest carts are in-memory models. Read their explicitly attached
        // relation so totals are calculated from the submitted guest items.
        $items = $this->resource->relationLoaded('items')
            ? $this->resource->getRelation('items')
            : collect();

        return [
            // 'id' => $this->id,
            // 'user_id' => $this->user_id,
            // 'status' => $this->status,
            'items' => GuestCartItemResource::collection($items),
            'total_before_discount' => (float) $items->sum('total_before_discount'),
            'total_after_discount' => (float) $items->sum('total_after_discount'),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
