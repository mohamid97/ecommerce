<?php

namespace App\Http\Requests\Api\Ecommerce\Option;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OptionStoreRequest extends FormRequest
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
            'option_image'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'title' => 'required|array|min:1',
            'title.*'=>'required|max:255',
            'code'=>'required|string|max:255|unique:options,code',
            'value_type'=>'required|in:text,code,image',
            'values'=>'required|array|min:1',
            'values.*.title'=>'required|array|min:1',
            'values.*.title.*'=>'required|max:255',
            

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