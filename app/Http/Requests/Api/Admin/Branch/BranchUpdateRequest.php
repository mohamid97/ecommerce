<?php

namespace App\Http\Requests\Api\Admin\Branch;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BranchUpdateRequest extends FormRequest
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
            'images'=> 'nullable|array',
            'images.*' => 'required|image|mimes:jpeg,webp,png,jpg,gif,svg|max:2048',
            'numbers'=> 'required|array',
            'numbers.*'=> 'required|min:10|max:14',
            'location'=>'required|max:2000',
            'status'=>'required|in:open,closed,pending',
            'title'=>'required|array',
            'title.*'=>'required|string|max:255',
            'des.*'=>'nullable|string|max:50000'
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
