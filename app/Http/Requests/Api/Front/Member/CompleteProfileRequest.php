<?php

namespace App\Http\Requests\Api\Front\Member;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompleteProfileRequest extends FormRequest
{
    use ResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('government') && $this->has('goverment')) {
            $this->merge(['government' => $this->input('goverment')]);
        }
    }

    public function rules(): array
    {
        return [
            'government' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'city' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'building_number' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'apartment_number' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
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
