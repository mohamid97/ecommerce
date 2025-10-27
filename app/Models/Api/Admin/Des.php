<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Des extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['des_image' , 'breadcrumb'];
    public $translatedAttributes = ['title','small_des','des' , 'alt_image' , 'title_image' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'des_id';
    public $translationModel = 'App\Models\Api\Admin\DesTranslation';
}