<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Points;

use Illuminate\Foundation\Http\FormRequest;

class StorePointsSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'min_order_amount' => 'required|numeric|min:0',
            'points' => 'required|integer|min:0',
            'pound_per_point' => 'required|numeric|min:0',
        ];
    }
}
