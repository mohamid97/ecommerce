<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Option extends Model implements TranslatableContract
{
    use HasFactory , Translatable;    
    protected $fillable = ['option_image'];
    public $translatedAttributes = ['title'];
    public $translationForeignKey = 'option_id';
    public $translationModel = 'App\Models\Api\Ecommerce\OptionTranslation';
}