<?php

namespace App\Http\Requests\Api\Ecommerce\Productoption;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductoptionUpdateRequest extends FormRequest
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
        $optionId = $this->input('id');
    

        
        return [
            'id'=>'required|exists:product_options,id',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:product_options,sku,' . $optionId,
            'options' => 'required|array|min:1',
            'options.*.option_id' => 'required|exists:options,id',
            'options.*.option_value_id'=>'required|exists:option_values,id',
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