<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovment extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'variant_id',
        'quantity',
        'cost_price',
        'sale_price',
        'note',
        'status',
    ];

    // need to make relations is product and variant
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // if out of stock
    public function isOutOfStock()
    {
        return $this->quantity <= 0;
    }
}
