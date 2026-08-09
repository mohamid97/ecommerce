<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Product\Variant;

use App\Models\Api\Ecommerce\GerneralVariantGalleries;
use App\Models\Api\Ecommerce\OptionValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSpecialImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imageIds' => ['required', 'array', 'min:1'],
            'imageIds.*' => ['required', 'integer', 'distinct', 'exists:gerneral_variant_galleries,id'],
            'options' => ['nullable', 'array'],
            'options.*.optionId' => ['required', 'integer', 'distinct', 'exists:options,id'],
            'options.*.valueIds' => ['required', 'array', 'min:1'],
            'options.*.valueIds.*' => ['required', 'integer', 'distinct', 'exists:option_values,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $imageIds = $this->input('imageIds', []);
            $productIds = GerneralVariantGalleries::whereIn('id', $imageIds)
                ->pluck('product_id')
                ->unique();

            if ($productIds->count() !== 1) {
                $validator->errors()->add('imageIds', 'All selected images must belong to the same product.');
            }

            foreach ($this->input('options', []) as $index => $option) {
                $valueIds = $option['valueIds'] ?? [];
                $validValueCount = OptionValue::where('option_id', $option['optionId'] ?? null)
                    ->whereIn('id', $valueIds)
                    ->count();

                if ($validValueCount !== count(array_unique($valueIds))) {
                    $validator->errors()->add("options.$index.valueIds", 'Each value must belong to the selected option.');
                }
            }
        });
    }
}
