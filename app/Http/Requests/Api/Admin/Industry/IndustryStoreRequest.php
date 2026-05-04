<?php

namespace App\Http\Requests\Api\Admin\Industry;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndustryStoreRequest extends FormRequest
{
    use ResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'industry_image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'title' => 'required|array|min:1',
            'title.*' => 'required|string|max:255',
            'slug' => 'nullable|array|min:1',
            'slug.*' => 'nullable|string|max:255',
            'small_des.*' => 'nullable|max:255',
            'des.*' => 'nullable|max:5000',
            'alt_image.*' => 'nullable|max:255',
            'title_image.*' => 'nullable|max:255',
            'meta_title.*' => 'nullable|max:255',
            'meta_des.*' => 'nullable|max:255',
            'order' => 'nullable|integer|unique:industries,order',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->error($validator->errors()->first(), 422)
        );
    }
}
