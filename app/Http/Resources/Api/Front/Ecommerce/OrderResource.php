<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'shipping_cost' => (float) $this->shipping_cost,
            'tax' => (float) $this->tax,
            'total' => (float) $this->total,
            'items' => $this->items->map(function ($item) {
                $payload = [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total_price' => (float) $item->total_price,
                    'batches' => $item->batches->map(function ($b) {
                        return [
                            'stock_movment_id' => $b->stock_movment_id,
                            'quantity' => $b->quantity,
                            'sale_price' => (float) $b->sale_price,
                            'cost_price' => (float) $b->cost_price,
                        ];
                    }),
                ];

                if (!empty($item->bundel_snapshot)) {
                    $payload['type'] = 'bundle';
                    $payload['bundel'] = $item->bundel_snapshot;
                } elseif ($item->bundel_id) {
                    // fallback to live relation if snapshot missing
                    $payload['type'] = 'bundle';
                    $payload['bundel'] = $item->bundel ? [
                        'id' => $item->bundel->id,
                        'price' => (float) $item->bundel->price,
                        'details' => $item->bundel->bundelDetails->map(function ($d) {
                            return [
                                'product_id' => $d->product_id,
                                'product' => $d->product ? [
                                    'id' => $d->product->id,
                                    'title' => $d->product->translate(app()->getLocale())->title ?? null,
                                    'sale_price' => (float) ($d->product->sale_price ?? 0),
                                    'stock' => $d->product->stock ?? null,
                                ] : null,
                                'variant_ids' => $d->variant_ids,
                                'variants' => $d->getVariants()->map(function ($v) {
                                    return [
                                        'id' => $v->id,
                                        'sale_price' => (float) $v->sale_price,
                                        'stock' => $v->stock ?? null,
                                    ];
                                }),
                                'quantity' => $d->quantity,
                            ];
                        }),
                    ] : null;
                } else {
                    $payload['type'] = 'product';
                    $payload['product_id'] = $item->product_id;
                    $payload['product'] = $item->product ? [
                        'id' => $item->product->id,
                        'title' => $item->product->translate(app()->getLocale())->title ?? null,
                        'sale_price' => (float) ($item->product->sale_price ?? 0),
                        'stock' => $item->product->stock ?? null,
                    ] : null;

                    if ($item->variant_id) {
                        $payload['variant_id'] = $item->variant_id;
                        $payload['variant'] = $item->variant ? [
                            'id' => $item->variant->id,
                            'sale_price' => (float) ($item->variant->sale_price ?? 0),
                            'stock' => $item->variant->stock ?? null,
                        ] : null;
                    }
                }

                return $payload;
            }),
        ];
    }
}
