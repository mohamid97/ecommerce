<?php

namespace App\Http\Requests\Api\Admin\Coupon;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CouponStoreRequest extends FormRequest
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
            'name' => 'required|array|min:1',
            'name.*'=>'required|max:255',
            'des.*'=>'nullable|max:5000',
            'code'=>'required|unique:coupons,code',
            'usage_limit'       => 'nullable|integer|min:1',
            'is_active'         => 'boolean',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'min_order_amount'  => 'nullable|numeric|min:1',
            'type'              => 'required|in:fixed,percentage',
            'value'             => 'required|numeric|min:0',


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