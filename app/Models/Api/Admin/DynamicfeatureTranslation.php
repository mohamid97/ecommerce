<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicfeatureTranslation extends Model
{
    use HasFactory;

    protected $table = 'dynamic_feature_translations';
    protected $fillable = ['title', 'small_des', 'des'];
}
