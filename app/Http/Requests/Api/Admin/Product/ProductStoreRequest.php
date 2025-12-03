<?php

namespace App\Http\Requests\Api\Admin\Product;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductStoreRequest extends FormRequest
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

            'base_price' => 'required|numeric|min:0',
            'category_id'=>'nullable|exists:categories,id',
            'brand_id'=>'nullable|exists:brands,id',
            'order'=>'nullable|integer|unique:service,order',
            'images'=>'nullable|array',
            'images.*'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'breadcrumb'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'product_image'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'title' => 'required|array|min:1',
            'title.*'=>'required|max:255',
            'small_des' => 'nullable|max:255',
            'des.*'=>'nullable|max:5000',
            'meta_title.*' => 'nullable|max:255',
            'meta_des.*' => 'nullable|max:255',
            'has_options' => 'required|boolean',
            'status'=>'nullable|in:published,pending',
            'stock'=>"required_if:has_options,true|nullable_if:has_options,false",
            'sku'=>"required_if:has_options,true|nullable_if:has_options,false",
            'product_options'=>'required_if:has_options,true|nullable_if:has_options,false|array',
            'product_options.*.option_id'=>'required_if:has_options,true|nullable_if:has_options,false|exists:options,id',
            'product_options.*.value_ids'=>'required_if:has_options,true|nullable_if:has_options,false|array',
            'product_options.*.value_ids.*'=>'required_if:has_options,true|nullable_if:has_options,false|exists:option_values,id',

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