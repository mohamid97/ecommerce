<?php

namespace App\Http\Requests\Api\Front;

use Illuminate\Foundation\Http\FormRequest;

class ConsultationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'industry_id' => 'nullable|integer|exists:industries,id',
            'note' => 'nullable|string|max:2000',
        ];
    }
}
