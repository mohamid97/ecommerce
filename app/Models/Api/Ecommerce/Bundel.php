<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Brand;
use App\Models\Api\Admin\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Bundel extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['price' , 'category_id' , 'brand_id' , 'bundle_image' , 'status'];
    public $translatedAttributes = ['title' , 'slug' , 'des' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'bundel_id';
    public $translationModel = 'App\Models\Api\Ecommerce\BundelTranslation';

    public function bundelDetails()
    {
       return $this->hasMany(BundelDetails::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

}
