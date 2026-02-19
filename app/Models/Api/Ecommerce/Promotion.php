<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Brand;
use App\Models\Api\Admin\Category;
use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Promotion extends Model implements TranslatableContract
{

    use HasFactory , Translatable;
    protected $fillable = [
        'start_date',
        'end_date',
        'status',
        'is_coupon',
        'coupon_code',
        'coupon_limit',
        'type',
        'location',
        'target',
        'discount',
        'max_amount_discount',
        'product_id',
        'brand_id',
        'categories',
        'customer_group'
        
    ];
    public $translatedAttributes = ['title' , 'des'];
    public $translationForeignKey = 'promotion_id';
    public $translationModel = 'App\Models\Api\Ecommerce\PromotionTranslation';
    public $casts = ['categories' => 'array'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'promotion_category', 'promotion_id', 'category_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    
   
}
