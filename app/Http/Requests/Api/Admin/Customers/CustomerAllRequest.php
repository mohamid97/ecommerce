<?php

namespace App\Http\Requests\Api\Admin\Customers;

use Illuminate\Foundation\Http\FormRequest;

class CustomerAllRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => 'nullable|integer|min:1|max:200',
            'search' => 'nullable|string|max:255',
        ];
    }
}
