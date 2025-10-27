<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Offer extends Model implements TranslatableContract
{
    use HasFactory , Translatable;

    protected $fillable = ['product_ids' , 'offer_image' , 'images' , 'breadcrumb'];
    public $translatedAttributes = ['title' , 'slug' , 'des'];
    public $translationForeignKey = 'location_id';
    public $translationModel = 'App\Models\Api\Admin\OfferTranslation';
    protected $casts = [
      'product_ids' => 'array',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }



    
}