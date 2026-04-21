<?php

namespace App\Http\Requests\Api\Ecommerce\Cart;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class DeleteFromCartRequest extends FormRequest
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
           'product_id'        => 'required_without:bundel_id|integer|exists:products,id',
           'variant_id'        => 'nullable|integer|exists:product_variants,id',
           'bundel_id'         => 'nullable|required_without:product_id|integer|exists:bundels,id',
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
