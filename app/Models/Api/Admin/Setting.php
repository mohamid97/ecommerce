<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Setting extends Model implements TranslatableContract
{
    use HasFactory , Translatable;

    protected $fillable = ['work_hours' , 'icon' , 'favicon'];
    public $translatedAttributes = ['title' , 'breif' , 'meta_des' , 'meta_title'];
    public $translationForeignKey = 'setting_id';
    public $translationModel = 'App\Models\Api\Admin\SettingTranslation';



    
    

}