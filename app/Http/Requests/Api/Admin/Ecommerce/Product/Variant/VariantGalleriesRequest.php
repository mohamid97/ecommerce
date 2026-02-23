<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Product\Variant;

use Illuminate\Foundation\Http\FormRequest;

class VariantGalleriesRequest extends FormRequest
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
            'variant_id' => 'required|exists:variants,id',
            'image_id' => 'required|exists:gerneral_variant_galleries,id',
        ];
    }


    
}
