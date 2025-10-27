<?php

namespace App\Http\Requests\Api\Admin\Slider;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SliderStoreRequest extends FormRequest
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
            'image'=>'required|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'order'=>'nullable|integer|unique:sliders,order',
            'title.*'=>'nullable|max:255',
            'small_des.*'=>'nullable|max:255',
            'des.*'=>'nullable|max:5000',
            'video' => 'nullable|file|mimes:mp4,avi,mkv|max:50000',
            'link' => 'nullable|url',
            'alt_image.*' => 'nullable|max:255',
            'title_image.*' => 'nullable|max:255',
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