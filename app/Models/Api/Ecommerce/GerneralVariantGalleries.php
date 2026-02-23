<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GerneralVariantGalleries extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'product_id',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class , 'product_id' , 'id');
    }
}
