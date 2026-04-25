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
            'shipment_city_id' => 'required|exists:shipment_cities,id',
            'shipment_zone_id' => 'required|exists:shipment_zones,id',
            'shipment_address' => 'required|string|max:255',
            'payment_method' => 'required|string|max:255',
            'use_points' => 'nullable|boolean',
            'points_to_use' => 'nullable|integer|min:0|max:1000',
            
        ];
    }
}
