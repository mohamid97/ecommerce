<?php

namespace App\Http\Requests\Api\Admin\Expense;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpenseStoreRequest extends FormRequest
{
    use ResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|array',
            'title.*' => 'required|string|max:255',
            'type' => 'required|in:fixed,variable',
            'amount' => 'required_if:type,fixed|nullable|numeric|min:0',
            'data' => 'required_if:type,variable|nullable|array',
            'data.*.date' => 'nullable|date',
            'data.*.amount' => 'required_with:data|numeric|min:0',
            'data.*.note' => 'nullable|string|max:1000',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->error($validator->errors()->first(), 422)
        );
    }
}
