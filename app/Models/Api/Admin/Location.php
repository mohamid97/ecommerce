<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Location extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['location'];
    public $translatedAttributes = ['address' , 'government','country'];
    public $translationForeignKey = 'location_id';
    public $translationModel = 'App\Models\Api\Admin\LocationTranslation';

    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }

    public function phones(){
        return $this->hasMany(Locationcontact::class , 'location_id');
    }

    public function emails(){
        return $this->hasMany(LocationEmail::class , 'location_id');
    }

    



}