<?php

namespace App\Http\Requests\Api\Admin\Ecommerce\Product\Variant;

use Illuminate\Foundation\Http\FormRequest;

class ImportVariantsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => ['required','file'],
            // optional: if the client provides a default product id to apply to all rows
            'default_product_id' => ['nullable','integer','exists:products,id'],
        ];
    }
}
