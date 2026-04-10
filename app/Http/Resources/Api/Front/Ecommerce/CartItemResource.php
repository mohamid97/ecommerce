<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

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

        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product' => $this->product?->title,
            'has_options'=>(bool) $this->product?->has_options,
            'variant' => $this->variant?->title,
            'varaint_name' => ($this->varaint_id) ? $this->buildVariantName($this->variant) : null,
            'total_before_discount' => $this->total_before_discount,
            'total_after_discount' => $this->total_after_discount,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];




    }






    protected function buildVariantName()
    {
        if($this->product?->has_options && $this->variant) {
            return $this->variant?->variants
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
