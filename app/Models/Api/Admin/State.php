<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class State extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['state_code' , 'country_id'];
    public $translatedAttributes = ['name'];
    public $translationForeignKey = 'state_id';
    public $translationModel = 'App\Models\Api\Admin\StateTranslation';


    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}