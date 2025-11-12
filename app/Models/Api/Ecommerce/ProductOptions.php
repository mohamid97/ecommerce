<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductOptions extends Model 
{
    use HasFactory;
    protected $fillable = ['product_id','stock','sku','price'  , 'option_id', 'option_value_id' ];

    public function product()
    {
        return $this->belongsTo(\App\Models\Api\Admin\Product::class, 'product_id');
    }

    public function options()
    {
        return $this->hasMany(\App\Models\Api\Ecommerce\ProductOptionValue::class, 'product_option_id');       
    }

    // public function optionValue()
    // {
    //     return $this->belongsTo(\App\Models\Api\Ecommerce\OptionValue::class, 'option_value_id');

    // }



    
}