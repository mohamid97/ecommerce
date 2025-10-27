<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicContact extends Model
{
    use HasFactory;
    protected $fillable = ['phones', 'emails'];
    protected $casts = [
    'phones' => 'array',
    'emails' => 'array',
    ];

}