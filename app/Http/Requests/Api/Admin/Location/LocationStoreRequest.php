<?php

namespace App\Http\Requests\Api\Admin\Location;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LocationStoreRequest extends FormRequest
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
            'location'=>'nullable|string|url',
            'address'=>"required|array|min:1",
            "address.*"=>'required|string:max:255',
            'counrty'=>"nullable|array|min:1",
            "counrty.*"=>'nullable|string:max:255',
            'government'=>"nullable|array|min:1",
            "government.*"=>'nullable|string:max:255',
            "phones.*"=>'nullable',
            'emails.*'=>'nullable'
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