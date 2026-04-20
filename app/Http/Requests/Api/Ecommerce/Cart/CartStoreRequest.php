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
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'bundel_id'  => 'nullable|integer|exists:bundels,id',
            'quantity'   => 'required|integer|min:1|max:50',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $data = $validator->validated();

            $hasProduct = !empty($data['product_id']);
            $hasVariant = !empty($data['variant_id']);

            // variant_id without product_id makes no sense
            if ($hasVariant && !$hasProduct) {
                $validator->errors()->add(
                    'variant_id',
                    __('validation.required_with', [
                        'attribute' => 'variant_id',
                        'values'    => 'product_id',
                    ])
                );
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->error($validator->errors()->first(), 422)
        );
    }
}