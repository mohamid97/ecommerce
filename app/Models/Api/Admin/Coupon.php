<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Coupon extends Model implements TranslatableContract
{
    use HasFactory, Translatable;
        protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'usage_limit',
        'is_active',
        'start_date',
        'end_date',
        'count'
    ];
    public $translatedAttributes = ['name','des'];
    public $translationForeignKey = 'coupon_id';
    public $translationModel = 'App\Models\Api\Admin\CouponTranslation';

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime', 
        'end_date' => 'datetime',
    ];

    


    
}