<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id','stock_movment_id','quantity','sale_price','cost_price'
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function stockMovment()
    {
        return $this->belongsTo(\App\Models\Api\Ecommerce\StockMovment::class, 'stock_movment_id');
    }
}
