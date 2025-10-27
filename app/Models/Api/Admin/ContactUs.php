<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class ContactUs extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['image' , 'breadcrumb'];
    public $translatedAttributes = ['title' , 'alt_image' , 'title_image' , 'des' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'contatcus_id';
    public $translationModel = 'App\Models\Api\Admin\ContactUsTranslation';

    
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
      
    }
    
    
}