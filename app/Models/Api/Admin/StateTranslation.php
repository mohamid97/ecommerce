<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateTranslation extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

}