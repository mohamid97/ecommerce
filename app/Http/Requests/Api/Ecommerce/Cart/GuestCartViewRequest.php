<?php

namespace App\Http\Requests\Api\Ecommerce\Cart;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GuestCartViewRequest extends FormRequest
{
    use ResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $products = collect($this->input('products', []))
            ->map(function ($product) {
                if (!is_array($product)) {
                    return $product;
                }

                if (array_key_exists('quantity:', $product) && !array_key_exists('quantity', $product)) {
                    $product['quantity'] = $product['quantity:'];
                    unset($product['quantity:']);
                }

                return $product;
            })
            ->values()
            ->all();

        $bundles = $this->input('bundles', []);

        if (is_array($bundles) && $bundles !== [] && array_keys($bundles) !== range(0, count($bundles) - 1)) {
            $bundles = [$bundles];
        }

        $bundles = collect($bundles)
            ->map(function ($bundle) {
                if (!is_array($bundle)) {
                    return $bundle;
                }

                if (array_key_exists('quantity:', $bundle) && !array_key_exists('quantity', $bundle)) {
                    $bundle['quantity'] = $bundle['quantity:'];
                    unset($bundle['quantity:']);
                }

                return $bundle;
            })
            ->values()
            ->all();

        $this->merge([
            'products' => $products,
            'bundles' => $bundles,
        ]);
    }

    public function rules(): array
    {
        return [
            'products' => 'nullable|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.variant_id' => 'nullable|integer|exists:product_variants,id',
            'products.*.quantity' => 'required|integer|min:1|max:50',
            'bundles' => 'nullable|array',
            'bundles.*.bundle_id' => 'required|integer|exists:bundels,id',
            'bundles.*.quantity' => 'required|integer|min:1|max:50',
            'bundles.*.bundle_items' => 'required|array|min:1',
            'bundles.*.bundle_items.*.product_id' => 'required|integer|exists:products,id',
            'bundles.*.bundle_items.*.variant_id' => 'nullable|integer|exists:product_variants,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (empty($this->input('products', [])) && empty($this->input('bundles', []))) {
                $validator->errors()->add('cart', 'At least one product or bundle is required.');
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
