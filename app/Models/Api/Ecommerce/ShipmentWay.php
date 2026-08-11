<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ShipmentWay extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $fillable = ['status', 'capacity'];
    public $translatedAttributes = ['title', 'des'];
    public $translationForeignKey = 'way_id';
    public $translationModel = 'App\Models\Api\Ecommerce\ShipmentWayTranslation';

    protected $casts = [
        'capacity' => 'integer',
    ];
}