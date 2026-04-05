<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreRelatedProductsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'id'=>'required|exists:products,id',
           'related_products'=>'required|array',
           'related_products.*'=>'required|exists:products,id'
        ];
    }
}
