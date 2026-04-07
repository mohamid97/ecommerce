<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Shipment;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CityStoreRequest extends FormRequest
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
            'zone_id'=>'required|exists:shipment_zones,id',
            'title' => 'required|array|min:1',
            'title.*'=>'required|max:255',
            'status'=> 'nullable|in:active,draft,unavailable',
            'des' => 'nullable|array|min:1',
            'des.*'=>'nullable|max:5000',

        ];
         dd('request end');
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
