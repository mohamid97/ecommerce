<?php

namespace App\Services\Ecommerce\Order\Strategies;

use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\Order;
use App\Models\Api\Ecommerce\StockMovment;
use App\Models\Api\Ecommerce\OrderItem;
use App\Models\Api\Ecommerce\OrderItemBatch;
use App\Services\Ecommerce\Order\OrderRepository;

class VariantItemStrategy implements CartItemStrategyInterface
{
    public function handle(CartItem $cartItem, Order $order, OrderRepository $repo): array
    {
        // get qty and product/variant details
        $qty = (int) $cartItem->quantity;
        $product = $cartItem->product;
        $variant = $cartItem->variant;
        $salePrice = $variant ->sale_price ?? 0;
        $priceAfterDiscount = $variant ->getDiscountPrice() ?? $salePrice;
        $orderItem = $repo->createOrderItem([
            'order_id' => $order->id,
            'product_id' => $product?->id,
            'variant_id' => $variant?->id,
            'bundel_id' => null,
            'quantity' => $qty,
            'sale_price' => $salePrice,
            'price_after_discount' => $priceAfterDiscount,
            'total_price' => $salePrice * $qty,
            'total_price_after_discount' => $priceAfterDiscount * $qty,
        ]);

        // allocate stock for variant (FIFO)
        $remaining = $qty;
        $batches = StockMovment::where('product_id', $product->id)->where('variant_id', $variant->id)->where('quantity', '>', 0)->orderBy('id')->get();
        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            $take = min($batch->quantity, $remaining);
            if ($take <= 0) continue;


            $repo->createOrderItemBatch([
                'order_item_id' => $orderItem->id,
                'stock_movment_id' => $batch->id,
                'quantity' => $take,
                'sale_price' => $batch->sale_price,
                'cost_price' => $batch->cost_price,
            ]);

            $batch->quantity -= $take;
            $batch->save();

            $variant->stock = max(0, $variant->stock - $take);
            $variant->save();

            $remaining -= $take;
        }

        if ($remaining > 0) {
            throw new \Exception(__('main.out_of_stock', ['product' => $product->id]));
        }

        return [(float) $orderItem->total_price, (float) $orderItem->total_price_after_discount];
    }

    public function supports(CartItem $cartItem): bool
    {
        return !empty($cartItem->variant_id);
    }
}
