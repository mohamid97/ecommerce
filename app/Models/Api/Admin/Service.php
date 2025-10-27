<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Service extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $fillable = [ 'service_image', 'breadcrumb' , 'order' , 'price'  , 'category_id'];
    public $translatedAttributes = ['title' , 'slug' , 'small_des' ,'des' , 'meta_title' , 'meta_des' , 'alt_image', 'title_image'];
    public $translationForeignKey = 'service_id';
    public $translationModel = 'App\Models\Api\Admin\ServiceTranslation';


    
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }



}