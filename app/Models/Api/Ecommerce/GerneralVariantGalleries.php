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
        'alt_text',
        'order',
    ];

    protected $casts = [
        'alt_text' => 'array',
    ];

    public function variantImage(){
        return $this->hasMany(ProductVaraintImages::class , 'image_id' , 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class , 'product_id' , 'id');
    }

    public function specialImageOptions()
    {
        return $this->hasMany(SpecialImageOptionSelection::class, 'image_id');
    }
}
