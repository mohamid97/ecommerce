<?php

namespace App\Http\Requests\Api\Front\DynamicFilter;

use Illuminate\Foundation\Http\FormRequest;

class DynamicFilterRequest extends FormRequest
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
            'model'     => 'required|string',
            'filter'    => 'required|array',
            'filter.*.column'  => 'required|string',
            'filter.*.value' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_string($value) && !is_numeric($value)) {
                        $fail("The $attribute must be a string or number.");
                    }
                },
            ],
            'pagination' => 'sometimes|nullable|integer',
            'order'     => 'nullable|in:asc,desc,ASC,DESC',
        ];
    }
}