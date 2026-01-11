<?php

namespace App\Http\Requests\Api\Admin\Product;


use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;



class ProductUpdateRequest extends FormRequest
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
            'id'=>'required|exists:products,id',
            'product_image'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'images'=>'nullable|array',
            'images.*'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'breadcrumb'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'status'=>'nullable|in:published,pending',
            'has_options' => 'required|boolean',
            'category_id'=>'nullable|exists:categories,id',
            'brand_id'=>'nullable|exists:brands,id',
            'order' => 'nullable|integer|unique:products,order,' . $this->id,
            'on_demand'=>'nullable|boolean',
            'cost_price' => 'nullable|numeric|min:0',
            'sales_price'=>'nullable|numeric|min:0',
            'discount'=>'nullable|numeric|min:0',
            'discount_type'=>'nullable|in:fixed,percentage',
            'sku'=>'nullable|string|max:255',
            'barcode'=>'nullable|string|max:255',
            'title' => 'required|array|min:1',
            'title.*'=>'required|max:255',
            'slug' => 'required|array|min:1',
            'slug.*'=>'required|max:255',
            'small_des'=>'nullable|array|min:1',
            'small_des.*'=>'nullable|string|max:255',
            'des'=>'nullable|array|min:1',
            'des.*'=>'nullable|max:5000',
            'meta_title'=>'nullable|array|min:1',
            'meta_title.*'=>'nullable|max:255',
            'meta_des'=>'nullable|array|min:1', 
            'meta_des.*'=>'nullable|max:255',



            'product_options'=>'nullable|required_if:has_options,true|array',
            'product_options.*.option_id'=>'nullable|required_if:has_options,true|exists:options,id',
            'product_options.*.value_ids'=>'nullable|required_if:has_options,true|array',
            'product_options.*.value_ids.*'=>'nullable|required_if:has_options,true|exists:option_values,id',


            'length'=>'nullable|numeric|min:0',
            'width'=>'nullable|numeric|min:0',
            'height'=>'nullable|numeric|min:0',
            'weight'=>'nullable|numeric|min:0',
            'min_estimated_delivery'=>'nullable|numeric|min:0',
            'max_estimated_delivery'=>'nullable|numeric|min:0',
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