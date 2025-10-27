<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Blog extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['blog_image','breadcrumb','is_active','category_id'];
    public $translatedAttributes = ['title', 'des' , 'small_des' , 'meta_title', 'meta_des','title_image' , 'alt_image', 'slug'];
    public $translationForeignKey = 'blog_id';
    public $translationModel = 'App\Models\Api\Admin\BlogTranslation';
    protected $casts = [
        'is_active' => 'boolean',
    ];

     protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    public function category(){
        return $this->belongsTo(Category::class , 'category_id');
    }
    
    
}