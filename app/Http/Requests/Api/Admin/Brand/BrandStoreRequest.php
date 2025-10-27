<?php

namespace App\Http\Requests\Api\Admin\Brand;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BrandStoreRequest extends FormRequest
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
            'image'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'breadcrumb'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'title' => 'required|array|min:1',
            'title.*'=>'required|max:255',
            'des.*'=>'nullable|max:5000',
            'alt_image.*' => 'nullable|max:255',
            'title_image.*' => 'nullable|max:255',
            'meta_title.*' => 'nullable|max:255',
            'meta_des.*' => 'nullable|max:255',
            'link'       =>'nullable|url|max:255',
            'slug' => 'nullable|array',
            'slug.*' => 'nullable|string|max:255|unique:blog_translations,slug',

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