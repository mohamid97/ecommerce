<?php

namespace App\Services\Ecommerce\Order\Strategies;

use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\Order;
use App\Models\Api\Ecommerce\StockMovment;
use App\Models\Api\Ecommerce\OrderItem;
use App\Models\Api\Ecommerce\OrderItemBatch;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\Bundel;
use App\Services\Ecommerce\Order\OrderRepository;

class BundleItemStrategy implements CartItemStrategyInterface
{
    public function handle(CartItem $cartItem, Order $order, OrderRepository $repo): array
    {
        // get qty and bundle details   
        $qty = (int) $cartItem->quantity;
        $bundle = Bundel::with('bundelDetails')->find($cartItem->bundel_id);
        [$sale_price , $price_after_discount] = $bundle?->getBundlePrice();

        $orderItem = $repo->createOrderItem([
            'order_id' => $order->id,
            'product_id' => null,
            'variant_id' => null,
            'bundel_id' => $bundle?->id,
            'quantity' => $qty,
            'sale_price' => $sale_price,
            'price_after_discount' => $price_after_discount,
            'total_price' => $sale_price * $qty,
            'total_price_after_discount' => $price_after_discount * $qty,
            'bundel_snapshot' => null,
        ]);

        if ($bundle) {
            $details = [];
            foreach ($bundle->bundelDetails as $d) {
                $variants = [];
                foreach ($d->getVariants() as $v) {
                    $variants[] = [
                        'id' => $v->id,
                        'sale_price' => (float) ($v->sale_price ?? 0),
                        'stock' => $v->stock ?? null,
                    ];
                }

                $details[] = [
                    'product_id' => $d->product_id,
                    'product' => $d->product ? [
                        'id' => $d->product->id,
                        'title' => $d->product->translate(app()->getLocale())->title ?? null,
                        'sale_price' => (float) ($d->product->sale_price ?? 0),
                        'stock' => $d->product->stock ?? null,
                    ] : null,
                    'variant_ids' => $d->variant_ids,
                    'variants' => $variants,
                    'quantity' => $d->quantity,
                ];
            }

            $orderItem->bundel_snapshot = [
                'id' => $bundle->id,
                'title' => $bundle->translate(app()->getLocale())->title ?? null,
                'price' => (float) $bundle->price,
                'details' => $details,
            ];
            $orderItem->save();
        }

        // allocate stock for each bundel detail
        foreach ($bundle?->bundelDetails ?? [] as $detail) {
            $selected = $cartItem->cartBundelItems->firstWhere('product_id', $detail->product_id);

            $subProduct = null;
            $subVariant = null;
            if ($selected) {
                $subProduct = $selected->product;
                $subVariant = $selected->variant;
            } else {
                if (!empty($detail->variant_ids) && is_array($detail->variant_ids)) {
                    $subVariant = ProductVariant::find($detail->variant_ids[0]);
                    $subProduct = $subVariant?->product;
                } else {
                    $subProduct = $detail->product;
                }
            }

            $need = $qty * ($detail->quantity ?? 1);
            $remaining = $need;

            $batchQuery = StockMovment::where('product_id', $subProduct->id)->where('quantity', '>', 0);
            if ($subVariant) {
                $batchQuery->where('variant_id', $subVariant->id);
            } else {
                $batchQuery->whereNull('variant_id');
            }

            $batches = $batchQuery->orderBy('id')->get();
            foreach ($batches as $batch) {
                if ($remaining <= 0) break;
                $take = min($batch->quantity, $remaining);
                if ($take <= 0) continue;


                $repo->createOrderItemBatch([
                    'order_item_id' => $orderItem->id,
                    'stock_movment_id' => $batch->id,
                    'quantity' => $take,
                    'sale_price' => $batch->sale_price ?? ($subVariant?->sale_price ?? $subProduct->sale_price ?? 0),
                    'cost_price' => $batch->cost_price ?? null,
                ]);

                $batch->quantity -= $take;
                $batch->save();

                if ($subVariant) {
                    $subVariant->stock = max(0, $subVariant->stock - $take);
                    $subVariant->save();
                } else {
                    $subProduct->stock = max(0, $subProduct->stock - $take);
                    $subProduct->save();
                }

                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new \Exception(__('main.out_of_stock', ['product' => $subProduct->id]));
            }
        }

        return [(float) $orderItem->total_price, (float) $orderItem->total_price_after_discount];
    }

    public function supports(CartItem $cartItem): bool
    {
        return !empty($cartItem->bundel_id);
    }
}
