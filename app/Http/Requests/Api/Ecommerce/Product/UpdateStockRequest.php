<?php

namespace App\Http\Requests\Api\Ecommerce\Product;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateStockRequest extends FormRequest
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
            'id' => 'required|exists:stock_movments,id',
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_options,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'nullable|in:increase,decrease',
            'note' => 'nullable|string',
            'cost_price' => 'nullable|numeric|min:0',
            'sales_price' => 'required|numeric|min:0',
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
