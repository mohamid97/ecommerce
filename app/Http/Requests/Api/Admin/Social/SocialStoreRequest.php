<?php

namespace App\Http\Requests\Api\Admin\Social;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SocialStoreRequest extends FormRequest
{
    use ResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        $platforms = [
            'facebook', 'twitter', 'instagram', 'youtube', 'linkedin',
            'tiktok', 'pinterest', 'snapchat', 'email', 'phone','whatsapp'
        ];

        foreach ($platforms as $platform) {
            $rules["{$platform}.value"] = 'nullable|string|max:255';
            $rules["{$platform}.cta"] = 'nullable|boolean';
            $rules["{$platform}.layout"] = 'nullable|boolean';
        }

        return $rules;
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