<?php

namespace App\Http\Requests\Api\Front\Order;

use Illuminate\Foundation\Http\FormRequest;

class GuestOrderPreviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.variant_id' => 'nullable|integer|exists:product_variants,id',
            'items.*.bundel_id' => 'nullable|integer|exists:bundels,id',
            'items.*.quantity' => 'required|integer|min:1|max:50000',
            'items.*.bundle_items' => 'nullable|array',
            'items.*.bundle_items.*.product_id' => 'required_with:items.*.bundel_id|integer|exists:products,id',
            'items.*.bundle_items.*.variant_id' => 'nullable|integer|exists:product_variants,id',
            'coupon_code' => 'nullable|string',
        ];
    }
}