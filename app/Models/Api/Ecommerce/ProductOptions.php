<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductOptions extends Model 
{
    use HasFactory;
    protected $fillable = ['product_id','stock','sku','price'];


    public function values()
    {
        return $this->hasMany(ProductOptionValues::class, 'product_option_id');
    }
    

    
}