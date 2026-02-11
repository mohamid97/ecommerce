<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Product\Variant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVaraintRequest extends FormRequest
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
            'id' => 'required|exists:product_variants,id',
            'sale_price'=>'nullable|numeric|min:0',
            'discount'=>'nullable|numeric|min:0',
            'discount_type'=>'nullable|in:fixed,percentage',
            'sku'=>'nullable|string|max:255',
            'barcode'=>'nullable|string|max:255',
            'title.*'=>'nullable|max:255',
            'title' => 'nullable|array|min:1',
            'slug.*'=>'nullable|max:255',
            'slug' => 'nullable|array|min:1',
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
