<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartBundelItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'cart_item_id',
        'product_id',
        'variant_id'

    ];


    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }


    
    
}
