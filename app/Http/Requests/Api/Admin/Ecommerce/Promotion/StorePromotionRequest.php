<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Promotion;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePromotionRequest extends FormRequest
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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
            'is_coupon' => 'required|boolean',
            'coupon_code' => 'nullable|string|unique:promotions,coupon_code',
            'coupon_limit' => 'nullable|integer|min:1|max:1000',
            'type' => 'required|in:percent,fixed,bundle,bulk,buy-x-get-y',
            'target' => 'required|in:global,category,product,brand,order',
            'location' => 'required|in:hero,offers_section,pop_up,header_alert',
            'image' => 'nullable|image|mimes:jpeg,png,webp,gif,svg|max:2048',
            'discount' => 'nullable|numeric|min:0',
            'max_amount_discount' => 'nullable|numeric|min:0',
            'title' => 'required|array',
            'title.*' => 'required|string|max:255',
            'des' => 'nullable|array',
            'des.*' => 'nullable|string',
            'meta_title' => 'nullable|array',
            'meta_title.*' => 'nullable|string|max:255',
            'meta_des' => 'nullable|array',
            'meta_des.*' => 'nullable|string|max:255',
            'customer_group' => 'nullable|in:all,new_user,registered',
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
