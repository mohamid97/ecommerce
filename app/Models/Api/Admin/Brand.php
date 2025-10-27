<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
 
class Brand extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['image' , 'breadcrumb'];
    public $translatedAttributes = ['title','slug','des' , 'alt_image' , 'title_image' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'brand_id';
    public $translationModel = 'App\Models\Api\Admin\BrandTranslation';

    
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'brand_category');
    }


    
}