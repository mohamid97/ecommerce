<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
 
class Mediaimage extends Model implements TranslatableContract
{
    use HasFactory , Translatable;

    protected $fillable = ['image'];
    public $translatedAttributes = ['title','des'];
    public $translationForeignKey = 'mediaimage_id';
    public $translationModel = 'App\Models\Api\Admin\MediaimageTranslation';

 
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }

    

    

}