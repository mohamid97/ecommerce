<?php

namespace App\Http\Requests\Api\Admin\Setting;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SettingUpdateStore extends FormRequest
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
            'work_hours'=>'nullable|string|max:255',
            'title'=>'required|array',
            'title.*'=>'required|string|max:255',
            'breif.*'=>'nullable|string|max:50000',
            'icon'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'favicon'=>'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:5000',
            'meta_des.*'=>'nullable|string|max:50000',
            'meta_title.*'=>'nullable|string|max:50000',



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