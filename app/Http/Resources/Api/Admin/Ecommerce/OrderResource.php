<?php

namespace App\Http\Resources\Api\Admin\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'order_number' => $this->order_number ?? null,
            'id' => $this->id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->userName(),
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'points' => (int) ($this->user->points ?? 0),
            ] : null,
            'customer' => [
                'id' => $this->user_id,
                'name' => $this->customerName(),
                'email' => $this->customerEmail(),
                'phone' => $this->customerPhone(),
                'type' => $this->user_id ? 'user' : 'guest',
            ],
            'guest_name' => $this->guest_name ?? null,
            'guest_email' => $this->guest_email ?? null,
            'phone' => $this->phone ?? $this->user?->phone ?? null,
            'status' => $this->status,
            'payment_status' => $this->payment_status ?? 'unpaid',
            'shipment_address' => $this->shipment_address,
            'government_id' => $this->government_id,
            'government' => $this->government ? [
                'id' => $this->government->id,
                'name' => ['ar' => $this->government->name_ar, 'en' => $this->government->name_en],
            ] : null,
            'shipment_zone_id' => $this->shipment_zone_id,
            'shipment_city_id' => $this->shipment_city_id,
            'payment_method' => $this->payment_method,
            'points_used' => (int) $this->points_used,
            'points_amount' => (float) $this->points_amount,
            'points_earned' => (int) ($this->points_earned ?? 0),

            'subtotal' => (float) $this->total_before_discount,
            'shipping_cost' => (float) $this->shipping_cost,
            'tax' => (float) $this->tax,
            'discount' => (float) ($this->discount ?? 0),
            'discount_type' => $this->discount_type ?? null,
            'coupon_code' => $this->coupon_code ?? null,
            'total_before_discount' => (float) $this->total_before_discount,
            'total_after_discount' => (float) $this->total_after_discount,
            'total' => (float) ($this->total_after_discount ?? $this->total),
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),
            'delivered_at' => $this->delivered_at,

            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    $payload = [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'sale_price' => (float) $item->sale_price,
                        'price_after_discount' => (float) $item->price_after_discount,
                        'total_price' => (float) $item->total_price,
                        'total_price_after_discount' => (float) $item->total_price_after_discount,
                        // 'batches' => $item->relationLoaded('batches') ? $item->batches->map(function ($b) {
                        //     return [
                        //         'stock_movment_id' => $b->stock_movment_id,
                        //         'quantity' => $b->quantity,
                        //         'sale_price' => (float) $b->sale_price,
                        //         'cost_price' => (float) $b->cost_price,
                        //     ];
                        // }) : [],
                    ];

                    if ($item->bundel_id) {
                        $payload['type'] = 'bundle';

                        if ($item->relationLoaded('orderBundelItems') && $item->orderBundelItems->count() > 0) {
                            $payload['bundle_id'] = $item->bundel?->id ?? null;
                            $payload['title'] = $item->bundel?->translate(app()->getLocale())->title ?? null;
                            $payload['image']     = $this->getImageUrl($item->bundel?->image);
                            $payload['bundle_details'] = $item->orderBundelItems->map(function ($obi) use ($item) {
                                $product = $obi->product;
                                $variant = $obi->variant;
                                $perBundleQty = $obi->quantity ?? 1;

                                return [
                                    'product_id' => $obi->product_id,
                                    'product' => $product ? [
                                        'id' => $product->id,
                                        'title' => $product->translate(app()->getLocale())->title ?? $product->title ?? null,
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
                        } elseif (!empty($item->bundel_snapshot)) {
                            $payload['bundle_id'] = $item->bundel_id;
                            $payload['bundle_details'] = $item->bundel_snapshot;
                        } else {
                            $payload['bundle_id'] = $item->bundel?->id ?? null;
                            $payload['bundle_price'] = (float) ($item->bundel?->price ?? $item->sale_price);
                            $payload['bundle_details'] = $item->bundel?->bundelDetails->map(function ($d) {
                                return [
                                    'product_id' => $d->product_id,
                                    'product' => $d->product ? [
                                        'id' => $d->product->id,
                                        'title' => $d->product->translate(app()->getLocale())->title ?? null,
                                    ] : null,
                                    'per_bundle_quantity' => $d->quantity,
                                    'total_quantity' => $d->quantity,
                                ];
                            }) ?? null;
                        }
                    } else {
                        $payload['type'] = ($item->variant_id) ? 'variant' : 'product';
                        $payload['product_id'] = $item->product_id;
                        $payload['image'] = $this->getImageUrl(($item->variant_id) ? $item->variant?->image : $item->product?->image);
                        $payload['product'] = $item->product ? [
                            'id' => $item->product->id,
                            'title' => $item->product->translate(app()->getLocale())->title ?? null,
                            'sale_price' => (float) ($item->product->sale_price ?? 0),
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
                });
            }),
        ];
    }

    private function customerName(): ?string
    {
        if ($this->user) {
            return $this->userName();
        }

        return $this->guest_name;
    }

    private function customerEmail(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    private function customerPhone(): ?string
    {
        return $this->phone ?? $this->user?->phone;
    }

    private function userName(): ?string
    {
        $fullName = trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? ''));

        return $this->user->name ?: ($fullName ?: null);
    }

    protected function buildVariantName($product, $variant): ?string
    {
        if ($product?->has_options && $variant && $variant->relationLoaded('variants')) {
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
