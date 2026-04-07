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
            'product_related',
            'related_product_id',
            'product_id'
        );
    }

}
