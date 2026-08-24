<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'status', 'payment_status', 'total_after_discount', 'total_before_discount', 'shipping_cost', 'tax', 'total', 'points_used', 'points_amount', 'points_earned', 'shipment_zone_id', 'shipment_city_id', 'shipment_address', 'payment_method', 'order_number', 'guest_name', 'guest_email', 'phone', 'discount', 'discount_type', 'coupon_code', 'delivered_at'
    ];
    

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\Api\Ecommerce\ShipmentCity::class, 'shipment_city_id');
    }

    public function zone()
    {
        return $this->belongsTo(\App\Models\Api\Ecommerce\ShipmentZone::class, 'shipment_zone_id');
    }
}
