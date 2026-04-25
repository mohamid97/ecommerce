<?php

namespace App\Http\Requests\Api\Front\Order;

use Illuminate\Foundation\Http\FormRequest;

class GuestOrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'shipment_city_id' => 'required|exists:shipment_cities,id',
            'shipment_address' => 'required|string',
            'payment_method' => 'required|string',
            'cart_id' => 'nullable|exists:carts,id',
        ];
    }
}
