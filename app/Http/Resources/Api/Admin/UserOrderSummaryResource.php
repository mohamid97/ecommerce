<?php

namespace App\Http\Resources\Api\Admin;

use App\Http\Resources\Api\Admin\Ecommerce\OrderListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOrderSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $user = $this->resource['user'];

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name ?: trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: null,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'type' => $user->type,
                'points' => (int) ($user->points ?? 0),
                'created_at' => $user->created_at?->format('Y-m-d'),
                'address'=>$user->profile?->address,
                'government'=>
                ['ar'=>$user->profile?->government?->name_ar,'en'=>$user->profile?->government?->name_en],
            ],
            'total_orders' => (int) $this->resource['total_orders'],
            'total_spent' => (float) $this->resource['total_spent'],
            'loyalty_points' => (int) ($user->points ?? 0),
            'latest_orders' => OrderListResource::collection($this->resource['latest_orders']),
        ];
    }
}
