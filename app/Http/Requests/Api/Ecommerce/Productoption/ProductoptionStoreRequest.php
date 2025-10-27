<?php

namespace App\Http\Requests\Api\Ecommerce\Productoption;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductoptionStoreRequest extends FormRequest
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
            'product_id'=>'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:product_options,sku',
            'options' => 'required|array|min:1',
            'options.*.option_name_id' => 'required|exists:options,id',
            'options.*.value'=>'required|array|min:1',
            'options.*.value.*' => 'required|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [

            'options.required' => 'At least one option must be provided',
            'sku.unique' => 'This SKU already exists'
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