<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    use HasFactory;
    protected $fillable = ['product_id' , 'option_id'];

    public function option()
    {
        return $this->belongsTo(Option::class , 'option_id' , 'id');
    }

    public function values()
    {
        return $this->hasMany(ProductOptionValue::class , 'product_option_id');
    }
}
