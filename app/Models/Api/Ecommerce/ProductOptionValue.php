<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    use HasFactory;
    protected $fillable = ['product_option_id','option_id','option_value_id'];

    public function option()
    {
        return $this->belongsTo(\App\Models\Api\Ecommerce\Option::class, 'option_id');
    }
    public function optionValue()
    {
        return $this->belongsTo(\App\Models\Api\Ecommerce\OptionValue::class, 'option_value_id');
    }


    public function productOption()
    {
        return $this->belongsTo(\App\Models\Api\Ecommerce\ProductOptions::class, 'product_option_id');
    }

    

    
}