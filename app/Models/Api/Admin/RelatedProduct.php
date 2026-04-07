<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedProduct extends Model
{
    use HasFactory;

    public function relatedTo()
    {
        return $this->belongsToMany(
            Product::class,
            'related_products',
            'related_product_id',
            'product_id'
        );
    }

        public function product(){
        return $this->belongsTo(Product::class , 'related_product_id');
    }
    

}
