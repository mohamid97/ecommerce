<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundelDetails extends Model
{
    use HasFactory;
    protected $fillable = ['bundel_id' , 'product_id' , 'variant_ids' , 'quantity'];

    public function bundel()
    {
        return $this->belongsTo(Bundel::class);
    }


    // JSON cast for variant_ids
    protected $casts = [
        'variant_ids' => 'array',
    ];


    // get variants 
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'id', 'variant_ids');
    }




}
