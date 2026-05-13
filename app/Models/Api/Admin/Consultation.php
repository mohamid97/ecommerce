<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $table = 'consultations';
    protected $fillable = [
        'full_name',
        'company_name',
        'email',
        'phone',
        'industry_id',
        'note',
    ];
}
