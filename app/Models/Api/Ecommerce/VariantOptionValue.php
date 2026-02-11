<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantOptionValue extends Model
{
    use HasFactory;
    protected $fillable = ['product_variant_id' , 'option_value_id'];
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function optionValue()
    {
        return $this->belongsTo(OptionValue::class , 'option_value_id' , 'id');
    }

    
}
