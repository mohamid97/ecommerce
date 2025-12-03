<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Promotion extends Model implements TranslatableContract
{

    use HasFactory , Translatable;
    protected $fillable = [
        'start_date' , 'end_date' , 'is_active','type_value',
        'min_order_value','order_discount_value','target',
        'category_id','brand_id','product_id' , 'is_coupon',
        'type','coupon_code' , 'location' , 'image'
    ];
    public $translatedAttributes = ['title' , 'des'];
    public $translationForeignKey = 'promotion_id';
    public $translationModel = 'App\Models\Api\Ecommerce\PromotionTranslation';
    
   
}
