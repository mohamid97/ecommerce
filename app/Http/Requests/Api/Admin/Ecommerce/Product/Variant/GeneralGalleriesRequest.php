<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Product\Variant;

use Illuminate\Foundation\Http\FormRequest;

class GeneralGalleriesRequest extends FormRequest
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
           'pro duct_id' => 'required|exists:products,id',
           'image' => 'required|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
        ];
    }
}
