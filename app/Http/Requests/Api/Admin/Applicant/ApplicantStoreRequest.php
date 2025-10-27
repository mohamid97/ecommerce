<?php

namespace App\Http\Requests\Api\Admin\Applicant;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApplicantStoreRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255'],
            'cv'         => ['required', 'file', 'mimes:pdf', 'max:5120'], // 5MB limit
            'phone'      => ['required', 'regex:/^01[0-2,5]{1}[0-9]{8}$/'], // Egyptian phone format
            'msg'        => ['nullable', 'string', 'max:10000'],
            'job_title'  => ['required', 'string', 'max:255'],
        ];
    }


    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',
            'email.required'      => 'Email is required.',
            'email.email'         => 'Email must be a valid email address.',
            'cv.required'         => 'CV is required.',
            'cv.mimes'            => 'CV must be a file of type: pdf, doc, docx.',
            'cv.max'              => 'CV must not be larger than 5MB.',
            'phone.required'      => 'Phone number is required.',
            'phone.regex'         => 'Phone number must be a valid Egyptian phone number.',
            'job_title.required'  => 'Job title is required.',
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
