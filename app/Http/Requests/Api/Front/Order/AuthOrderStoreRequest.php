<?php

namespace App\Http\Requests\Api\Front\Order;

use Illuminate\Foundation\Http\FormRequest;

class AuthOrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => 'nullable|string|max:20',
            'government' => 'required|string|max:255',
            'shipment_address' => 'required|string|max:500',
            'payment_method' => 'nullable|string|max:255',
            'use_points' => 'nullable|boolean',
            'points_to_use' => 'nullable|integer|min:0|max:1000',
            
        ];
    }
}
