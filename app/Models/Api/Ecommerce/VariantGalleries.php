<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantGalleries extends Model
{
    use HasFactory;
    protected $fillable = [
        'image_id',
        'varaint_id',
    ];

    public function image()
    {
        return $this->belongsTo(GerneralVariantGalleries::class , 'image_id' , 'id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class , 'varaint_id' , 'id');
    }
}
