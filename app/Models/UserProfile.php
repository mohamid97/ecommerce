<?php

namespace App\Models;

use App\Models\Api\Ecommerce\Gov;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'government_id',
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
    public function government()
    {
        return $this->belongsTo(Gov::class, 'government_id');
    }
}
