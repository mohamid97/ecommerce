<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ShipmentCity extends Model implements TranslatableContract
{
    use HasFactory , Translatable;

    protected $fillable = ['status' , 'zone_id'];
    public $translatedAttributes = ['title' , 'des'];
    public $translationForeignKey = 'city_id';
    public $translationModel = 'App\Models\Api\Ecommerce\ShipmentCityTranslation';
    
}
