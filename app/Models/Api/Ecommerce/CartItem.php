<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'total_before_discount',
        'total_after_discount',
    ];

    public static function createOrUpdate(array $where, array $data)
    {
        $item = self::where($where)->first();
        if ($item) {
            $item->update($data);
            return $item;
        }

        return self::create(array_merge($where, $data));
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');       

    }




}
