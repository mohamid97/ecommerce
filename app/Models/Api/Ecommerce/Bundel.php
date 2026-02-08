<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Bundel extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['price' , 'category_id' , 'brand_id' , 'bundle_image'];
    public $translatedAttributes = ['title' , 'slug' , 'des' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'bundel_id';
    public $translationModel = 'App\Models\Api\Ecommerce\BundelTranslation';

    public function details()
    {
       return $this->hasMany(BundelDetails::class);
    }

}
