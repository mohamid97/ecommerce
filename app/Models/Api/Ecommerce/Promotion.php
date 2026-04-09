<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Brand;
use App\Models\Api\Admin\Category;
use App\Models\Api\Admin\Product;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

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
        'image',
        'bundle_id',
        'customer_group',
    ];

    public $translatedAttributes = ['title', 'des', 'meta_title', 'meta_des'];

    public $translationForeignKey = 'promotion_id';

    public $translationModel = 'App\Models\Api\Ecommerce\PromotionTranslation';

    protected $casts = [
        'discount' => 'float',
        'max_amount_discount' => 'float',
        'is_coupon' => 'boolean',
        'status' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'promotion_brands', 'promotion_id', 'brand_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'promotion_categories', 'promotion_id', 'category_id');
    }
}
