<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantOptionValue extends Model
{
    use HasFactory;
    protected $fillable = ['product_variant_id', 'option_id', 'option_value_id'];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function optionValue()
    {
        return $this->belongsTo(OptionValue::class, 'option_value_id', 'id');
    }

    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id', 'id');
    }

    // Get product through productVariant relationship
    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            ProductVariant::class,
            'id', // Foreign key on ProductVariant table
            'id', // Foreign key on Product table
            'product_variant_id', // Local key on VariantOptionValue table
            'product_id' // Local key on ProductVariant table
        );
    }
}
