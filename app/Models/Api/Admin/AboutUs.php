<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class AboutUs extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['image' , 'breadcrumb'];
    public $translatedAttributes = ['title','small_des','mission','vission','brief','services' , 'alt_image' , 'title_image' , 'des' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'aboutus_id';
    public $translationModel = 'App\Models\Api\Admin\AboutUsTranslation';

    
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }
    
   
}