<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Client extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $fillable = ['image' , 'breadcrumb' , 'type','link'];
    public $translatedAttributes = ['title', 'alt_image', 'title_image', 'des'];
    public $translationForeignKey = 'client_id';
    public $translationModel = 'App\Models\Api\Admin\ClientTranslation';
}