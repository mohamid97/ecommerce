<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Statistics;

use Illuminate\Foundation\Http\FormRequest;

class RecentOrdersStatisticsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => 'nullable|integer|min:1|max:50',
        ];
    }
}
