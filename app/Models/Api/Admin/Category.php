<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Category extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['category_image', 'thumbnail','type' , 'order','breadcrumb' , 'parent_id'];
    public $translatedAttributes = ['title' , 'alt_image' , 'title_image' ,'small_des' , 'des' , 'meta_title' , 'meta_des' , 'slug'];
    public $translationForeignKey = 'category_id';
    public $translationModel = 'App\Models\Api\Admin\CategoryTranslation';

    
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }

    public function parent(){
      return $this->belongsTo(Category::class , 'parent_id');
    }
    public function childrens(){
        return $this->hasMany(Category::class , 'parent_id');
    }

      public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_category');
    }

    public function blogs(){
        return $this->hasMany(Blog::class , 'category_id');   
    }

    public function services(){
        return $this->hasMany(Service::class , 'category_id');

    }
    
    
}