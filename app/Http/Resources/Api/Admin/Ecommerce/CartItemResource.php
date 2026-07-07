<?php

namespace App\Http\Resources\Api\Admin\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Api\Ecommerce\BundelDetails;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->bundel_id && $this->type === 'bundle') {
            return [
                'id' => $this->id,
                'cart_id' => $this->cart_id,
                'type' => 'bundle',
                'bundel_id' => $this->bundel_id,
                'bundel_title' => $this->bundel?->title,
                'bundle_items' => $this->cartBundelItems->map(function($item) {
                    return [
                        'id' => $item->id,
                        'bundle_item_id' => $item->bundle_item_id,
                        'product_id' => $item->product_id,
                        'product' => $item->product?->title,
                        'variant_id' => $item->variant_id,
                        'variant' => $item->variant?->title,
                        'varaint_name' => $item->variant_id ? $this->buildVariantName($item->product, $item->variant) : null,
                        'quantity' => $item->bundleDetail?->quantity
                            ?? BundelDetails::where('bundel_id', $this->bundel_id)
                                ->where('product_id', $item->product_id)
                                ->value('quantity')
                            ?? 1,
                    ];
                }),
                'total_before_discount' => (float) $this->total_before_discount,
                'total_after_discount' => (float) $this->total_after_discount,
                'quantity' => (float) $this->quantity,
                'created_at' => $this->created_at->format('Y-m-d'),
                'updated_at' => $this->updated_at->format('Y-m-d'),
            ];
        }

        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'type' => 'product',
            'product_id' => $this->product_id,
            'product' => $this->product?->title,
            'product_image' => $this->getImageUrl($this->product?->product_image),
            'has_options' => (bool) $this->product?->has_options,
            'variant_id' => $this->variant_id,
            'variant' => $this->variant?->title,
            'varaint_name' => ($this->variant_id) ? $this->buildVariantName($this->product, $this->variant) : null,
            'discount'=>(float) ($this->variant?->discount_value ?? $this->product?->discount ?? 0),
            'discount_type' => $this->variant?->discount_type ?? $this->product?->discount_type,
            'status' => $this->variant?->status ?? $this->product?->status,
            'total_before_discount' => $this->total_before_discount,
            'total_after_discount' => $this->total_after_discount,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
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
