<?php

namespace App\Http\Resources\Api\Admin\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name ?? $this->user->first_name . ' ' . $this->user->last_name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
            ] : null,
            'status' => $this->status,
            'shipment_address' => $this->shipment_address,
            'payment_method' => $this->payment_method,
            'points_used' => (int) $this->points_used,
            'points_amount' => (float) $this->points_amount,
            
            'subtotal' => (float) $this->subtotal,
            'shipping_cost' => (float) $this->shipping_cost,
            'tax' => (float) $this->tax,
            'total_before_discount' => (float) $this->total_before_discount,
            'total_after_discount' => (float) $this->total_after_discount,
            'total' => (float) $this->total,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),

            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    $payload = [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'sale_price' => (float) $item->sale_price,
                        'price_after_discount' => (float) $item->price_after_discount,
                        'total_price' => (float) $item->total_price,
                        'total_price_after_discount' => (float) $item->total_price_after_discount,
                        'batches' => $item->relationLoaded('batches') ? $item->batches->map(function ($b) {
                            return [
                                'stock_movment_id' => $b->stock_movment_id,
                                'quantity' => $b->quantity,
                                'sale_price' => (float) $b->sale_price,
                                'cost_price' => (float) $b->cost_price,
                            ];
                        }) : [],
                    ];

                    if (!empty($item->bundel_snapshot)) {
                        $payload['type'] = 'bundle';
                        $payload['bundel'] = $item->bundel_snapshot;
                    } elseif ($item->bundel_id) {
                        $payload['type'] = 'bundle';
                        if ($item->relationLoaded('bundel') && $item->bundel) {
                            $payload['bundel'] = [
                                'id' => $item->bundel->id,
                                'price' => (float) $item->bundel->price,
                                'title' => $item->bundel->translate(app()->getLocale())->title ?? null,
                                'details' => $item->bundel->relationLoaded('bundelDetails') ? $item->bundel->bundelDetails->map(function ($d) {
                                    return [
                                        'product_id' => $d->product_id,
                                        'product' => ($d->relationLoaded('product') && $d->product) ? [
                                            'id' => $d->product->id,
                                            'title' => $d->product->translate(app()->getLocale())->title ?? null,
                                        ] : null,
                                        'quantity' => $d->quantity,
                                        'variant_ids' => $d->variant_ids,
                                    ];
                                }) : [],
                            ];
                        }
                    } else {
                        $payload['type'] = 'product';
                        $payload['product_id'] = $item->product_id;
                        if ($item->relationLoaded('product') && $item->product) {
                            $payload['product'] = [
                                'id' => $item->product->id,
                                'title' => $item->product->translate(app()->getLocale())->title ?? null,
                            ];
                        }

                        if ($item->variant_id) {
                            $payload['variant_id'] = $item->variant_id;
                            if ($item->relationLoaded('variant') && $item->variant) {
                                $payload['variant'] = [
                                    'id' => $item->variant->id,
                                    'title' => $item->variant->translate(app()->getLocale())->title ?? null,
                                ];
                            }
                        }
                    }

                    return $payload;
                });
            }),
        ];
    }
}
