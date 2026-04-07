<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ShipmentZone extends Model implements TranslatableContract
{
    use HasFactory , Translatable;

    protected $fillable = ['status' , 'price'];
    public $translatedAttributes = ['title' , 'des'];
    public $translationForeignKey = 'zone_id';
    public $translationModel = 'App\Models\Api\Ecommerce\ShipmentZoneTranslation';
}
