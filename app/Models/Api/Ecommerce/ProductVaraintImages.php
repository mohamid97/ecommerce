<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVaraintImages extends Model
{
    use HasFactory;
    protected $fillable = ['variant_id','image_id'];

    public function image(){
        return $this->belongsTo(GerneralVariantGalleries::class , 'image_id');
    }
}
