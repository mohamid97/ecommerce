<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Product\Variant;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BulkStoreVariantsRequest extends FormRequest
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
            'shared_data' => 'nullable|array',
            'shared_data.sale_price' => 'nullable|numeric|min:0',
            'shared_data.discount' => 'nullable|numeric|min:0',
            'shared_data.discount_type' => 'nullable|in:fixed,percentage',
            'shared_data.title' => 'nullable|array',
            'shared_data.slug' => 'nullable|array',
            'shared_data.des' => 'nullable|array',
            'shared_data.meta_title' => 'nullable|array',
            'shared_data.meta_des' => 'nullable|array',
            'shared_data.image_ids' => 'nullable|array',
            'shared_data.image_ids.*' => 'nullable|exists:gerneral_variant_galleries,id',
            'shared_data.length' => 'nullable|numeric|min:0',
            'shared_data.width' => 'nullable|numeric|min:0',
            'shared_data.height' => 'nullable|numeric|min:0',
            'shared_data.weight' => 'nullable|numeric|min:0',
            'shared_data.delivery_time' => 'nullable|integer|min:0',
            'shared_data.max_time' => 'nullable|integer|min:0',
            'shared_data.moq' => 'nullable|integer|min:1',
            'variants' => 'required|array|min:1',
            'variants.*' => 'required|array',
            'variants.*.option_value_ids' => 'required|array|min:1',
            'variants.*.option_value_ids.*' => 'required|exists:option_values,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->error($validator->errors()->first(), 422)
        );
    }
}
