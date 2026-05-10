<?php

namespace App\Http\Requests\Api\Admin\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class OrderUserSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
        ];
    }
}
