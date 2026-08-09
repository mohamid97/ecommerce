<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Model;

class SpecialImageOptionSelection extends Model
{
    protected $fillable = ['image_id', 'option_id', 'option_value_id'];

    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    public function optionValue()
    {
        return $this->belongsTo(OptionValue::class, 'option_value_id');
    }
}
