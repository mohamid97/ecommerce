<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'government',
        'address',
        'city',
        'area',
        'building_number',
        'floor',
        'apartment_number',
        'landmark',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
