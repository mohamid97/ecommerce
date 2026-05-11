<?php

namespace App\Http\Requests\Api\Admin\Customers;

use Illuminate\Foundation\Http\FormRequest;

class CustomerOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'per_page' => 'nullable|integer|min:1|max:200',
            'status' => 'nullable|string',
        ];
    }
}
