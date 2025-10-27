<?php

namespace App\Http\Requests\Api\Admin\Ourteam;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OurteamUpdateRequest extends FormRequest
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
            'social.*'=>'nullable|url|max:255',
            'social.facebook'=>'nullable|url|max:255',
            'social.twitter'=>'nullable|url|max:255',
            'social.linkedin'=>'nullable|url|max:255',
            'social.instagram'=>'nullable|url|max:255',
            'social.youtube'=>'nullable|url|max:255',
            'social.tiktok'=>'nullable|url|max:255',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5000',
            'name'=>'required|array|min:1',
            'name.*'=>'required|string|max:255',
            'position'=>'nullable|array|min:1',
            'position.*'=>'nullable|string|max:255',
            'experience'=>'nullable|array|min:1',
            'experience.*'=>'nullable|string|max:255',
            'des'=>'nullable|array|min:1',
            'des.*'=>'nullable|string|max:5000',
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