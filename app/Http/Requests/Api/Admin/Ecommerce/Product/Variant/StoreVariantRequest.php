<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Product\Variant;

use Illuminate\Foundation\Http\FormRequest;

class StoreVariantRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'option_value_ids' => 'required|array',
            'option_value_ids.*' => 'required|exists:option_values,id',
            'cost_price' => 'nullable|numeric|min:0',
            'sale_price'=>'nullable|numeric|min:0',
            'discount'=>'nullable|numeric|min:0',
            'discount_type'=>'nullable|in:fixed,percentage',
            'sku'=>'nullable|string|max:255',
            'barcode'=>'nullable|string|max:255',
            'title' => 'required|array|min:1',
            'title.*'=>'required|max:255',
            'small_des'=>'nullable|array|min:1',
            'small_des.*'=>'nullable|string|max:255',
            'des'=>'nullable|array|min:1',
            'des.*'=>'nullable|max:5000',
            'meta_title'=>'nullable|array|min:1',
            'meta_title.*'=>'nullable|max:255',
            'meta_des'=>'nullable|array|min:1', 
            'meta_des.*'=>'nullable|max:255',

        ];
    }
}
