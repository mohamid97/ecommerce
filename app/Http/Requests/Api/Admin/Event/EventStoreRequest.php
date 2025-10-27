<?php

namespace App\Http\Requests\Api\Admin\Event;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EventStoreRequest extends FormRequest
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
            'event_image' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif,svg|max:2048',
            'breadcrumb' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif,svg|max:2048',
            'date' => 'nullable|date',
            'meta_title.*' => 'nullable|string|max:255',
            'meta_des.*' => 'nullable|string|max:255',
            'alt_image' => 'nullable|string|max:255',
            'title_image' => 'nullable|string|max:255',
            'title' => 'required|array',
            'title.*' => 'required|string|max:255',
            'des.*' => 'nullable|string|max:5000',
            'slug.*' => 'nullable|string|max:255|unique:event_translations,slug',

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
