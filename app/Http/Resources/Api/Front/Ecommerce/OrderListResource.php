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
            'payment_method' => $this->payment_method,
            'items_count' => $this->items_count ?? null,
            'subtotal' => (float) $this->total_before_discount,
            'shipping_cost' => (float) $this->shipping_cost,
            'tax' => (float) $this->tax,
            'discount' => (float) ($this->discount ?? 0),
            'discount_type' => $this->discount_type ?? null,
            'coupon_code' => $this->coupon_code ?? null,
            'points_used' => (int) ($this->points_used ?? 0),
            'points_amount' => (float) ($this->points_amount ?? 0),
            'points_earned' => (int) ($this->points_earned ?? 0),
            'total_before_discount' => (float) $this->total_before_discount,
            'total_after_discount' => (float) $this->total_after_discount,
            'total' => (float) ($this->total_after_discount ?? $this->total),
            'delivered_at' => $this->delivered_at,
            'created_at' => $this->created_at?->format('Y-m-d') ?? null,
            'updated_at' => $this->updated_at?->format('Y-m-d') ?? null,
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    $payload = [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'sale_price' => (float) $item->sale_price,
                        'price_after_discount' => (float) $item->price_after_discount,
                        'total_price' => (float) $item->total_price,
                        'total_price_after_discount' => (float) $item->total_price_after_discount,
                    ];

                    if ($item->bundel_id) {
                        $payload['type'] = 'bundle';
                        $payload['image'] = $this->getImageUrl($item->bundel?->image);
                        $payload['title'] = $item->bundel?->translate(app()->getLocale())->title ?? null;

                        if ($item->orderBundelItems && $item->orderBundelItems->count() > 0) {
                            $payload['bundle_id'] = $item->bundel?->id ?? null;
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
                            $payload['bundle_id'] = $item->bundel?->id ?? null;
                            $payload['bundle_price'] = (float) ($item->bundel?->price ?? $item->sale_price);
                            $payload['bundle_details'] = $item->bundel?->bundelDetails->map(function ($detail) {
                                return [
                                    'product_id' => $detail->product_id,
                                    'product' => $detail->product ? [
                                        'id' => $detail->product->id,
                                        'title' => $detail->product->title ?? null,
                                    ] : null,
                                    'per_bundle_quantity' => $detail->quantity,
                                    'total_quantity' => $detail->quantity,
                                ];
                            }) ?? null;
                        }
                    } else {
                        $payload['type'] = ($item->variant_id) ? 'variant' : 'product';
                        $payload['image'] = $this->getImageUrl($item->variant?->image ?? $item->product?->image);
                        $payload['product_id'] = $item->product_id;
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

    protected function buildVariantName($product, $variant)
    {
        if ($product?->has_options && $variant) {
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
