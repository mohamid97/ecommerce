<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Page extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['page_image','position' , 'breadcrumb'];
    public $translatedAttributes = ['title','slug' , 'small_des','des' , 'alt_image' , 'title_image' , 'des' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'page_id';
    public $translationModel = 'App\Models\Api\Admin\PageTranslation';



}
