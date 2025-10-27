<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationEmail extends Model
{
    use HasFactory;
     protected $fillable = ['email'];
}