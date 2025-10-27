<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Ourwork extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $fillable = ['ourwork_image' , 'link','type','breadcrumb','client_id','category_id' , 'date'];
    public $translatedAttributes = ['title' , 'des' , 'meta_title' , 'meta_des' , 'slug','small_des' , 'location'];
    public $translationForeignKey = 'ourwork_id';
    public $translationModel = 'App\Models\Api\Admin\OurworkTranslation';


    
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }

    public function client(){
        return $this->belongsTo(Client::class , 'client_id' , 'id');
    }

    public function category(){
        return $this->belongsTo(Category::class , 'category_id' , 'id');
    }
    

    
}