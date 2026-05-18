<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gov extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'government_id');
    }
}
