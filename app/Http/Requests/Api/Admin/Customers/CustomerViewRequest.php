<?php

namespace App\Http\Requests\Api\Admin\Customers;

use Illuminate\Foundation\Http\FormRequest;

class CustomerViewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:users,id',
        ];
    }
}
