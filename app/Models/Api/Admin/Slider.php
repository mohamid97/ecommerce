<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Slider extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = [
        'image' , 'link' , 'order' , 'video'
    ];
    public $translatedAttributes = ['title' , 'alt_image' , 'title_image' ,'small_des' , 'des'];
    public $translationForeignKey = 'slider_id';
    public $translationModel = 'App\Models\Api\Admin\SliderTranslation';

    
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }


    
}