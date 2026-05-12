<?php

namespace App\Http\Requests\Api\Front\Order;

use Illuminate\Foundation\Http\FormRequest;

class GuestOrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'government' => 'required|string|max:255',
            'shipment_address' => 'required|string|max:1000',
            'payment_method' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.variant_id' => 'nullable|integer|exists:product_variants,id',
            'items.*.bundel_id' => 'nullable|integer|exists:bundels,id',
            'items.*.quantity' => 'required|integer|min:1|max:50',
            'items.*.bundle_items' => 'nullable|array',
            'items.*.bundle_items.*.product_id' => 'required_with:items.*.bundel_id|integer|exists:products,id',
            'items.*.bundle_items.*.variant_id' => 'nullable|integer|exists:product_variants,id',
            'coupon_code' => 'nullable|string',
        ];
    }
}
