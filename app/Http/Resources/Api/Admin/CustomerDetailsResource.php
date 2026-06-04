<?php

namespace App\Http\Resources\Api\Admin;

use App\Http\Resources\Api\Admin\Ecommerce\OrderListResource;
use Illuminate\Http\Request;

class CustomerDetailsResource extends CustomerResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'profile' => [
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
            'paid_orders_count' => (int) ($this->paid_orders_count ?? 0),
            'latest_orders' => OrderListResource::collection($this->whenLoaded('latestOrders')),
            'updated_at' => $this->updated_at?->format('Y-m-d'),
        ]);
    }
}
