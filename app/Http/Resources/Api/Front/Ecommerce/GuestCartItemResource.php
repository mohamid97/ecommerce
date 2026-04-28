<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use App\Models\Api\Ecommerce\BundelDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuestCartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if ($this->bundel_id && $this->type === 'bundel') {
            return [
                // 'id' => $this->id,
                // 'cart_id' => $this->cart_id,
                'type' => 'bundle',
                'bundel_id' => $this->bundel_id,
                'bundel_title' => $this->bundel?->title,
                'bundle_items' => $this->cartBundelItems->map(function ($item) {
                    return [
                        // 'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product' => $item->product?->title,
                        'variant_id' => $item->variant_id,
                        'variant' => $item->variant?->title,
                        'varaint_name' => $item->variant_id ? $this->buildVariantName($item->product, $item->variant) : null,
                        'quantity' => BundelDetails::where('bundel_id', $this->bundel_id)
                            ->where('product_id', $item->product_id)
                            ->value('quantity') ?? 1,
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
            // 'id' => $this->id,
            // 'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'type' => $this->type,
            'product' => $this->product?->title,
            'has_options' => (bool) $this->product?->has_options,
            'variant_id' => $this->variant_id,
            'variant' => $this->variant?->title,
            'varaint_name' => $this->variant_id ? $this->buildVariantName($this->product, $this->variant) : null,
            'total_before_discount' => (float) $this->total_before_discount,
            'total_after_discount' => (float) $this->total_after_discount,
            'quantity' => (float) $this->quantity,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }

    protected function buildVariantName($product, $variant)
    {
        if ($product?->has_options && $variant) {
            return $variant->variants
                ->map(function ($variantOptionValue) {
                    $optionTitle = optional($variantOptionValue->optionValue?->option)->title;
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
