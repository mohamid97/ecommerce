<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'order_number' => $this->order_number ?? null,
            'id' => $this->id,
            'guest_name' => $this->guest_name ?? null,
            'guest_email' => $this->guest_email ?? null,
            'phone' => $this->phone ?? $this->user?->phone ?? null,
            'status' => $this->status,
            'government_id' => $this->government_id,
            'government' => $this->government ? [
                'id' => $this->government->id,
                'name' => ['ar' => $this->government->name_ar, 'en' => $this->government->name_en],
            ] : null,
            'subtotal' => (float) $this->total_before_discount,
            'shipping_cost' => (float) $this->shipping_cost,
            'tax' => (float) $this->tax,
            'discount' => (float) ($this->discount ?? 0),
            'discount_type' => $this->discount_type ?? null,
            'coupon_code' => $this->coupon_code ?? null,
            'total_before_discount' => (float) $this->total_before_discount,
            'total_after_discount' => (float) $this->total_after_discount,
            'total' => (float) ($this->total ?? $this->total_after_discount + $this->shipping_cost + $this->tax),
            'payment_status' => $this->payment_status ?? 'unpaid',
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

                    // prefer live bundle relation when available
                    if ($item->bundel) {
                        $payload['image'] = $this->getImageUrl($item->bundel->image);

                        if ($item->orderBundelItems && $item->orderBundelItems->count() > 0) {
                            $payload['bundle_id'] = $item->bundel->id ?? null;
                            $payload['bundle_details'] = $item->orderBundelItems->map(function ($obi) use ($item) {
                                $product = $obi->product;
                                $variant = $obi->variant;
                                $perBundleQty = $obi->quantity ?? 1;

                                return [
                                    'product_id' => $obi->product_id,
                                    'product' => $product ? [
                                        'id' => $product->id,
                                        'title' => $product->title ?? null,
                                    ] : null,
                                    'selected_variant_id' => $obi->variant_id,
                                    'selected_variant' => $variant ? [
                                        'id' => $variant->id,
                                        'variant_name' => $this->buildVariantName($product, $variant),
                                    ] : null,
                                    'per_bundle_quantity' => $perBundleQty,
                                    'total_quantity' => $perBundleQty * $item->quantity,
                                ];
                            });
                        } else {
                            $payload['bundle_id'] = $item->bundel->id ?? null;
                            $payload['bundle_price'] = (float) ($item->bundel->price ?? $item->sale_price);
                            $payload['bundle_details'] = $item->bundel->bundelDetails->map(function ($d) {
                                return [
                                    'product_id' => $d->product_id,
                                    'product' => $d->product ? [
                                        'id' => $d->product->id,
                                        'title' => $d->product->title ?? null,
                                    ] : null,
                                    'per_bundle_quantity' => $d->quantity,
                                    'total_quantity' => $d->quantity, // frontend can multiply by order item qty
                                ];
                            }) ?? null;
                        }

                    } elseif (!empty($item->bundel_snapshot)) {
                        // live bundle missing -> use snapshot
                        $payload['bundle_id'] = $item->bundel_id;
                        $payload['bundle_price'] = (float) ($item->bundel_snapshot['price'] ?? $item->sale_price);
                        $payload['bundle_details'] = $item->bundel_snapshot['details'] ?? null;

                    } else {
                        $payload['bundle_id'] = null;
                        $payload['bundle_details'] = null;
                    }
                } else {
                    $payload['type'] = ($item->variant_id) ? 'variant' : 'product';
                    $payload['image'] = $this->getImageUrl($item->variant?->image ?? $item->product?->image);
                    $payload['product_id'] = $item->product_id;
                    // prefer live relation, otherwise use snapshot fields
                    if ($item->product) {
                        $payload['product'] = [
                            'id' => $item->product->id,
                            'title' => $item->product->translate(app()->getLocale())->title ?? null,
                            'sale_price' => (float) ($item->product->sale_price ?? 0),
                        ];
                    } elseif (!empty($item->product_name) || !empty($item->product_snapshot)) {
                        $payload['product'] = [
                            'id' => $item->product_id,
                            'title' => $item->product_name ?? ($item->product_snapshot['title'] ?? null),
                            'sale_price' => (float) ($item->product_price ?? ($item->product_snapshot['sale_price'] ?? 0)),
                        ];
                    } else {
                        $payload['product'] = null;
                    }

                    if ($item->variant_id) {
                        $payload['variant_id'] = $item->variant_id;
                        if ($item->variant) {
                            $payload['variant'] = [
                                'id' => $item->variant->id,
                                'variant_name' => $this->buildVariantName($item->product, $item->variant),
                            ];
                        } else {
                            $payload['variant'] = [
                                'id' => $item->variant_id,
                                'variant_name' => $item->variant_combination_name ?? null,
                            ];
                        }
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

