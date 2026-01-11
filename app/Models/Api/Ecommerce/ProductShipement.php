<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductShipement extends Model
{
    use HasFactory;
    protected $fillable = ['product_id' , 'shipement_id' , 'weight' , 'length' , 
    'width' , 'height' , 'min_estimated_delivery' , 'max_estimated_delivery'];

    public function product()
    {
        return $this->belongsTo(Product::class , 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class , 'variant_id');
    }
}
