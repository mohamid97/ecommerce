<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVaraintStatusRequest extends FormRequest
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
            'product_id'=>'nullable|exists:products,id',
            'varaint_id'=>'nullable|exists:product_variants,id',
            'status'=>'required|in:active,draft,unavailable'
        ];

    }
}
