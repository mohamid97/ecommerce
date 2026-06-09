<?php

namespace App\Http\Requests\Api\Admin\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class OrderAllRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paginate' => 'nullable|integer|min:1|max:200',
            'status' => 'nullable|string',
            'user_id' => 'nullable|integer|exists:users,id',
            'order_number' => 'nullable|string',
            'search' => 'nullable|string',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'payment_status' => 'nullable|string|in:paid,unpaid,refunded',
            'orderDirection' => 'nullable|in:asc,desc',
            'orderBy' => 'nullable|string|max:255',
        ];
    }
}
