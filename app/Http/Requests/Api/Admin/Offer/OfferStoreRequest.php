<?php

namespace App\Http\Requests\Api\Admin\Offer;

use Illuminate\Foundation\Http\FormRequest;

class OfferStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_ids'=>'required|array',
            'product_ids.*'=>'required|integer|exists:products,id',
            'images'=> 'nullable|array',
            'images.*' => 'required|image|mimes:jpeg,png,webp,jpg,gif,svg|max:2048',
            'breadcrumb' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif,svg|max:2048',
            'offer_image' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif,svg|max:2048',
            'title' => 'required|array',
            'title.*' => 'required|string|max:255',
            'des.*' => 'nullable|string|max:5000',
            'slug.*' => 'nullable|string|max:255',

        ];
    }
}