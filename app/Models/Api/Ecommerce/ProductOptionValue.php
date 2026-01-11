<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    use HasFactory;
    protected $fillable = ['product_option_id' , 'option_value_id'];


    public function productOption()
    {
        return $this->belongsTo(ProductOption::class);
    }
    public function optionValue()
    {
        return $this->belongsTo(OptionValue::class , 'option_value_id' , 'id');
    }
    

}
