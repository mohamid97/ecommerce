<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use App\Models\Api\Ecommerce\BundelDetails;
use App\Http\Resources\Api\Front\Ecommerce\Concerns\CalculatesCartMaximumQuantity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuestCartItemResource extends JsonResource
{
    use CalculatesCartMaximumQuantity;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $effectiveDiscount = [];
        $discountType = 'fixed';

        if ($this->bundel_id && $this->type === 'bundel') {
            $effectiveDiscount = $this->bundel->getEffectiveDiscount(
                (float) $this->total_before_discount,
                (float) $this->total_after_discount
            );
            $discountType = $effectiveDiscount['discount_type'] ?? 'fixed';
        }

        return [
            'bundel_title' => $this->bundel?->title,
            'bundle_items' => $this->cartBundelItems->map(function ($item) {
                return [
                    'quantity' => $item->bundleDetail?->quantity
                        ?? BundelDetails::where('bundel_id', $this->bundel_id)
                            ->where('product_id', $item->product_id)
                            ->value('quantity')
                        ?? 1,
                ];
            }),
            'sale_price' => (float) $this->total_before_discount,
            'price_after_discount' => (float) $this->total_after_discount,
            'total_before_discount' => (float) $this->total_before_discount,
            'total_after_discount' => (float) $this->total_after_discount,
            'discount' => $effectiveDiscount['discount'] ?? 0,
            'discount_type' => $discountType,
        ];
    }
}