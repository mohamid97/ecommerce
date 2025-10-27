<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Product extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $fillable = ['product_image','has_options' , 'status','breadcrumb' , 'order' , 'base_price'  , 'brand_id','category_id'];
    public $translatedAttributes = ['title' , 'slug' , 'small_des' ,'des' , 'meta_title' , 'meta_des' , 'alt_image', 'title_image'];
    public $translationForeignKey = 'product_id';
    public $translationModel = 'App\Models\Api\Admin\ProductTranslation';


    
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function options()
    {
        return $this->hasMany(\App\Models\Api\Ecommerce\ProductOptions::class, 'product_id');
    }


}