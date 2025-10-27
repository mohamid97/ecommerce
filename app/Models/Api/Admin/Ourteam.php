<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Ourteam extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['image' , 'facebook' , 'twitter' , 'linkedin' , 'instagram' ,'youtube' , 'tiktok'];
    public $translatedAttributes = ['position','name','experience','des'];
    public $translationForeignKey = 'ourteam_id';
    public $translationModel = 'App\Models\Api\Admin\OurteamTranslation';

    
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d');
    }

    
}