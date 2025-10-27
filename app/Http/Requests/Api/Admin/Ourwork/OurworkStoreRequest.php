<?php

namespace App\Http\Requests\Api\Admin\Ourwork;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OurworkStoreRequest extends FormRequest
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
            
            'breadcrumb'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'ourwork_image'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'title' => 'required|array|min:1',
            'title.*'=>'required|max:255',
            'slug' => 'nullable|array|min:1',
            'slug.*'=>'nullable|max:255',  
            'des.*'=>'nullable|max:5000',
            'meta_title.*' => 'nullable|max:255',
            'meta_des.*' => 'nullable|max:255',
            'link' => 'nullable|url|max:255', 
            'type'=>'required|in:ourworks,ourprojects',
            'client_id'=>'nullable|exists:clients,id', 
            'category_id'=>'nullable|exists:categories,id',
            'date'=>'nullable|date',
            'small_des.*'=>'nullable|max:5000',
            'location.*'=>'nullable|max:255',

            

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