<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'status', 'total_after_discount', 'total_before_discount', 'shipping_cost', 'tax', 'total', 'points_used', 'points_amount', 'shipment_zone_id', 'shipment_city_id', 'shipment_address', 'payment_method'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
