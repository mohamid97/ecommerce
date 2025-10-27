<?php

namespace App\Http\Requests\Api\Admin\Gallery;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreGallery extends FormRequest
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
           'new_images' => 'nullable|array',
           'new_images.*.file' => 'required|image|mimes:jpeg,webp,png,jpg,gif,svg|max:2048',
           'new_images.*.order' => 'nullable|integer',
           'old_order' =>'nullable|array',
           'old_order.*id' => 'nullable|integer',
           'old_order.*order' => 'nullable|integer',
           'model'=>'required'
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