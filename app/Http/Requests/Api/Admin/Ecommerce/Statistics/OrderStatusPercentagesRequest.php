<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Statistics;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusPercentagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => 'nullable|date_format:Y-m',
        ];
    }
}
