<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentWayZone extends Model
{
    use HasFactory;

    protected $fillable = ['way_id', 'zone_id', 'price', 'status'];

    protected $casts = [
        'price' => 'float',
    ];

    public function way()
    {
        return $this->belongsTo(ShipmentWay::class, 'way_id');
    }

    public function zone()
    {
        return $this->belongsTo(ShipmentZone::class, 'zone_id');
    }
}