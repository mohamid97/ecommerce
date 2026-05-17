<?php

namespace App\Models\Api\Admin;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $fillable = ['industry_image' , 'breadcrumb', 'order'];
    public $translatedAttributes = ['title', 'slug', 'small_des', 'des', 'alt_image', 'title_image', 'meta_title', 'meta_des'];
    public $translationForeignKey = 'industry_id';
    public $translationModel = 'App\Models\Api\Admin\IndustryTranslation';

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'industry_product');
    }
}
