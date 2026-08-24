<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'city',
        'area',
        'building_number',
        'floor',
        'apartment_number',
        'landmark',
        'notes',
        'city_id',
        'zone_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\Api\Ecommerce\ShipmentCity::class, 'city_id');
    }

    public function zone()
    {
        return $this->belongsTo(\App\Models\Api\Ecommerce\ShipmentZone::class, 'zone_id');
    }
}
