<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointsSetting extends Model
{
    use HasFactory;

    protected $table = 'points_settings';

    protected $fillable = [
        'min_order_amount',
        'points',
        'pound_per_point',
    ];

    protected $casts = [
        'min_order_amount' => 'float',
        'points' => 'integer',
        'pound_per_point' => 'float',
    ];
}
