<?php

namespace App\Http\Requests\Api\Admin\Mediaimage;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MediaimageStoreRequest extends FormRequest
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
           'image'=>'required|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
           'title'=>'nullable|array|min:1',
           'title.*'=>'nullable|string|max:255',
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