<?php

namespace App\Http\Requests\Api\Ecommerce\Cart;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CartStoreRequest extends FormRequest
{
    use ResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * product_id  → always required (plain product, product+variant, and bundle all need it)
     * variant_id  → nullable; required at the strategy layer ONLY if the product actually has options
     * bundel_id   → nullable; its presence switches to BundleStrategy
     * quantity    → always required
     */
    public function rules(): array
    {
        return [
            'product_id'   => 'nullable|integer|exists:products,id',
            'variant_id'   => 'nullable|integer|exists:product_variants,id',
            'bundel_id'    => 'nullable|required_without:product_id|integer|exists:bundels,id',
            'quantity'     => 'required|integer|min:1|max:50',
            'bundle_items' => 'nullable|array',
            'bundle_items.*.product_id' => 'nullable|integer|exists:products,id',
            'bundle_items.*.variant_id' => 'nullable|integer|exists:product_variants,id',
        ];
    }


    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->error($validator->errors()->first(), 422)
        );
    }
}