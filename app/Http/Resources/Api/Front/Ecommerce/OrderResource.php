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
            'subtotal' => (float) $this->total_before_discount,
            'shipping_cost' => (float) $this->shipping_cost,
            'tax' => (float) $this->tax,
            'total' => (float) $this->total_after_discount,
            'items' => $this->items->map(function ($item) {
                $payload = [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'sale_price' => (float) $item->sale_price,
                    'price_after_discount' => (float) $item->price_after_discount,
                    'total_price' => (float) $item->total_price,
                    'total_price_after_discount' => (float) $item->total_price_after_discount,
                    // 'unit_price' => (float) $item->unit_price,
                    // 'batches' => $item->batches->map(function ($b) {
                    //     return [
                    //         'stock_movment_id' => $b->stock_movment_id,
                    //         'quantity' => $b->quantity,
                    //         'sale_price' => (float) $b->sale_price,
                    //         'cost_price' => (float) $b->cost_price,
                    //     ];
                    // }),
                ];

                if ($item->bundel_id) {
                    $payload['type'] = 'bundle';
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
                                ] : null,
                                // here need to check if has varaint get varaint name that choosed in the bundle
                                
                                'quantity' => $d->quantity,
                            ];
                        }),
                    ] : null;
                } else {
                    $payload['type'] = ($item->variant_id) ? 'variant' : 'product';
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
                            'variant_name' => $this->buildVariantName($item->product, $item->variant),
                        ] : null;
                    }
                }

                return $payload;
            }),
        ];
    }



        protected function buildVariantName($product, $variant)
    {
        if($product?->has_options && $variant) {
            return $variant->variants
                ->map(function ($variantOptionValue) {
                    $optionTitle = optional(
                        $variantOptionValue->optionValue?->option
                    )->title;

                    $valueTitle = $variantOptionValue->optionValue?->title;

                    if (!$optionTitle || !$valueTitle) {
                        return null;
                    }

                    return $optionTitle . ' ' . $valueTitle;
                })
                ->filter()
                ->implode(' ');
        }
        return null;
    }



}



