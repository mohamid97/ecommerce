<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Shipment;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShipmentWayZoneUpdateRequest extends FormRequest
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
            'id' => 'required|exists:shipment_way_zones,id',
            'way_id' => 'required|exists:shipment_ways,id',
            'zone_id' => 'required|exists:shipment_zones,id',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,draft,unavailable',
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