<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Country extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['iso2' , 'iso3'];
    public $translatedAttributes = ['name'];
    public $translationForeignKey = 'country_id';
    public $translationModel = 'App\Models\Api\Admin\CountryTranslation';


    public function states()
    {
        return $this->hasMany(State::class);
    }


}