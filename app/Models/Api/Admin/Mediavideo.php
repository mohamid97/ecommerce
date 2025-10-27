<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Mediavideo extends Model implements TranslatableContract
{
    use HasFactory , Translatable;

    protected $fillable = ['link'];
    public $translatedAttributes = ['title','des'];
    public $translationForeignKey = 'mediavideo_id';
    public $translationModel = 'App\Models\Api\Admin\MediavideoTranslation';

 
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }



}