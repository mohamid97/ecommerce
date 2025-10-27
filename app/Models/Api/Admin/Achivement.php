<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Achivement extends Model implements TranslatableContract
{
        use HasFactory , Translatable;
    protected $fillable = ['achivement_image' , 'breadcrumb' , 'number'];
    public $translatedAttributes = ['title'  , 'des' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'achivement_id';
    public $translationModel = 'App\Models\Api\Admin\AchivementTranslation';

    
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }
}