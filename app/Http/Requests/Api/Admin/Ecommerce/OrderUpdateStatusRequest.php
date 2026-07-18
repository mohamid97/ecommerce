<?php

namespace App\Http\Requests\Api\Admin\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'sometimes|required_without:order_number|exists:orders,id',
            'order_number' => 'sometimes|required_without:id|exists:orders,order_number',
            'status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded,finished',
            'payment_status' => 'required|string|in:paid,unpaid,refunded',
        ];
    }
}
