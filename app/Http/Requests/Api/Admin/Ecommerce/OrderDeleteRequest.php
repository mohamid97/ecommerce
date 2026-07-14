<?php

namespace App\Http\Requests\Api\Admin\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class OrderDeleteRequest extends FormRequest
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
        ];
    }
}
