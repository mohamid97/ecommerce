<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Statistics;

use Illuminate\Foundation\Http\FormRequest;

class OverviewStatisticsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:daily,weekly,monthly,annually',
        ];
    }
}
