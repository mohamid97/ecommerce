<?php

namespace App\Http\Requests\Api\Admin\Dynamicfeature;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DynamicfeatureUpdateRequest extends FormRequest
{
    use ResponseTrait;

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
            'type' => 'nullable|string|in:train,consult',
            'icon' => 'nullable|image|mimes:png,jpg,webp,jpeg|max:5000',
            'title' => 'required|array',
            'title.*' => 'required|string|max:255',
            'small_des' => 'nullable|array',
            'small_des.*' => 'nullable|string|max:1000',
            'des' => 'nullable|array',
            'des.*' => 'nullable|string|max:10000',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->error(
                $validator->errors()->first(), 
                422
            )
        );
    }
}
