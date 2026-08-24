<?php

namespace App\Http\Requests\Api\Front\Member;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMemberRequest extends FormRequest
{
    use ResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:12',
            'city_id'=>'nullable|integer|exists:shipment_cities,id',
            'zone_id'=>'nullable|integer|exists:shipment_zones,id',
            'address'=>'nullable|string|max:5000',
            'building_number'=>'nullable|string|max:255',
            'floor'=>'nullable|string|max:255',
            'apartment_number'=>'nullable|string|max:255',
            'landmark'=>'nullable|string|max:255',
            'notes'=>'nullable|string|max:5000',
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
