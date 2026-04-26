<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id','product_id','variant_id','bundel_id','quantity','sale_price','total_price','price_after_discount','total_price_after_discount','bundel_snapshot'
    ];

    protected $casts = [
        'bundel_snapshot' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function batches()
    {
        return $this->hasMany(OrderItemBatch::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Api\Admin\Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(\App\Models\Api\Ecommerce\ProductVariant::class, 'variant_id');
    }

    public function bundel()
    {
        return $this->belongsTo(\App\Models\Api\Ecommerce\Bundel::class, 'bundel_id');
    }
}
