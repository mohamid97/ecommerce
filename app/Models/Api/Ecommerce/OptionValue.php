<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class OptionValue extends Model implements TranslatableContract
{
      use HasFactory , Translatable;    
    protected $fillable = ['option_id' , 'value'];
    public $translatedAttributes = ['title'];
    public $translationForeignKey = 'option_value_id';
    public $translationModel = 'App\Models\Api\Ecommerce\OptionValueTranslation';

    public function option()
    {
        return $this->belongsTo(Option::class , 'option_id' , 'id');
    }
}