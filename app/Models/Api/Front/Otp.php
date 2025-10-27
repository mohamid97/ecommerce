<?php

namespace App\Models\Api\Front;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = ['email', 'code', 'expires_at' , 'verfied'];
    protected $casts = [
        'expires_at' => 'datetime',
    ];
    use HasFactory;
}