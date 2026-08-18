<?php

namespace App\Http\Resources\Api\Admin\Ecommerce;

use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\BundelDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->bundel_id && $this->type === 'bundel') {
            $effectiveDiscount = $this->bundel->getEffectiveDiscount(
                (float) $this->total_before_discount,
                (float) $this->total_after_discount
            );

            return [
                'id' => $this->id,
                'cart_id' => $this->cart_id,
                'type' => 'bundle',
                'bundle_id' => $this->bundel_id,
                'title' => $this->bundel?->title,
                'bundle_image' => $this->getImageUrl($this->bundel?->bundle_image),
                'bundle_items' => $this->cartBundelItems->map(function($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product?->title,
                        'quantity' => $item->bundleDetail?->quantity,
                    ];
                }),
                'sale_price' => (float) $this->total_before_discount/$this->quantity,
                'price_after_discount' => (float) $this->total_after_discount/$this->quantity,
                'total_before_discount' => (float) $this->total_before_discount,
                'total_after_discount' => (float) $this->total_after_discount,
                'discount' => $effectiveDiscount['discount'],
                'discount_type' => $effectiveDiscount['discount_type'],
                'quantity' => (float) $this->quantity,
                'max_quantity' => $this->maximumQuantity(),
                'created_at' => $this->created_at->format('Y-m-d'),
                'updated_at' => $this->updated_at->format('Y-m-d'),
            ];
        }

        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'product_image' => $this->getImageUrl($this->product?->product_image),
            'type' => $this->type,
            'product' => $this->product?->title,
            'moq' => $this->product?->moq,
            'has_options' => (bool) $this->product?->has_options,
            'variant_id' => $this->variant_id,
            'variant' => $this->variant?->title,
            'variant_moq' => $this->variant?->moq,
            'sale_price' => (float) $this->total_before_discount/$this->quantity,
            'price_after_discount' => (float) $this->total_after_discount/$this->quantity,
            'total_before_discount' => (float) $this->total_before_discount,
            'total_after_discount' => (float) $this->total_after_discount,
            'quantity' => (float) $this->quantity,
            'max_quantity' => $this->maximumQuantity(),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}