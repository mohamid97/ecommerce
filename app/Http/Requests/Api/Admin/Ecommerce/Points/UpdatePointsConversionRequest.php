<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Points;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePointsConversionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pound_per_point' => 'required|numeric|min:0',
        ];
    }
}
