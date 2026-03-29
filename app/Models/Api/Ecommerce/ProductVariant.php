<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProductVariant extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    public $translatedAttributes = ['title' , 'slug' , 'des' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'product_variant_id';
    public $translationModel = 'App\Models\Api\Ecommerce\ProductVariantTranslation';
    protected $fillable = ['product_id' , 'sku' , 'barcode'  , 'status' , 'stock' , 'sale_price' , 'discount_value' , 'discount_type' , 'length' , 'width' , 'height' , 'weight' , 'delivery_time' , 'max_time' , 'images'];
    protected $casts = [
        'sale_price' => 'float',
        'discount_value' => 'float',
        'length' => 'float',
        'width' => 'float',
        'height' => 'float',
        'weight' => 'float',
    ];

     protected $appends = ['variant_full_name'];

     protected function serializeDate(\DateTimeInterface $date)
     {
       return $date->format('Y-m-d'); 
     }
        
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
   
    public function variants()
    {
        return $this->hasMany(VariantOptionValue::class);
    }


    public function varaintImages()
    {
        return $this->hasMany(ProductVaraintImages::class, 'variant_id');
    }
    
    
}
