<?php

namespace App\Http\Requests\Api\Admin\DynamicApi;

use Illuminate\Foundation\Http\FormRequest;

class DaynamicRequest extends FormRequest
{
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
                'model'   => 'required|string',
                'columns' => 'nullable|array',
                'order'   => 'nullable|string|in:asc,desc,ASC,DESC',
                'pagination' => 'nullable|integer|min:1',
        ];
    }
}