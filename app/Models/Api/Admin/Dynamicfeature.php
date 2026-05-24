<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Dynamicfeature extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $table = 'dynamic_features';
    protected $fillable = ['icon', 'type'];
    public $translatedAttributes = ['title', 'small_des', 'des'];
    public $translationForeignKey = 'dynamic_feature_id';
    public $translationModel = 'App\Models\Api\Admin\DynamicfeatureTranslation';

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d'); 
    }
}
