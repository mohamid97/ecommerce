<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Product\Stock;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BulkAddStockRequest extends FormRequest
{
    use ResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'variant_ids' => 'nullable|array',
            'variant_ids.*' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
            'cost_price' => 'nullable|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'status' => 'nullable|in:draft,active,inactive,depleted',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->error(
                $validator->errors()->first(),
                422,
            )
        );
    }
}
