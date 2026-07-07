<?php

namespace App\Services\Ecommerce\Order\Strategies;

use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\BundelDetails;
use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\Order;
use App\Models\Api\Ecommerce\OrderItemBundelItem;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\StockMovment;
use App\Services\Ecommerce\Order\OrderRepository;

class BundleItemStrategy implements CartItemStrategyInterface
{
    public function handle(CartItem $cartItem, Order $order, OrderRepository $repo): array
    {

        // get qty and bundle details
        $qty = (int) $cartItem->quantity;
        $bundle = Bundel::with('bundelDetails')->find($cartItem->bundel_id);

        [$sale_price , $price_after_discount] = $this->getBundlePrice($cartItem, $bundle);

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
            'product_name' => $bundle?->translate(app()->getLocale())->title ?? null,
            'product_sku' => null,
            'product_price' => $sale_price,
            'variant_combination_name' => null,
            'variant_sku' => null,
            'variant_price' => null,
            'variant_attributes' => null,
            'product_snapshot' => [
                'bundel_id' => $bundle?->id,
                'title' => $bundle?->translate(app()->getLocale())->title ?? null,
                'price' => (float) $bundle?->price,
            ],
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

        // persist selected bundle items for this order item
        $usedSelectionKeys = [];
        foreach ($bundle?->bundelDetails ?? [] as $detail) {
            $selected = $this->findSelectedBundleItem($cartItem->cartBundelItems, $detail, $usedSelectionKeys);
            if ($selected) {
                $usedSelectionKeys[] = $this->buildSelectionKey($selected);
            }

            $productId = $detail->product_id;
            $variantId = $selected?->variant_id ?? null;
            $perBundleQty = $detail->quantity ?? 1;

            // Store only the per-bundle quantity (quantity of this product inside one bundle)
            OrderItemBundelItem::create([
                'order_item_id' => $orderItem->id,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $perBundleQty,
            ]);
        }

        // allocate stock for each bundel detail
        $usedSelectionKeys = [];
        foreach ($bundle?->bundelDetails ?? [] as $detail) {
            $selected = $this->findSelectedBundleItem($cartItem->cartBundelItems, $detail, $usedSelectionKeys);
            if ($selected) {
                $usedSelectionKeys[] = $this->buildSelectionKey($selected);
            }

            $subProduct = null;
            $subVariant = null;
            if ($selected) {
                $subProduct = $selected->product;
                $subVariant = $selected->variant;
            } else {
                $detailVariantIds = $detail->selectedVariantIds();
                if (! empty($detailVariantIds)) {
                    $subVariant = ProductVariant::find($detailVariantIds[0]);
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
                if ($remaining <= 0) {
                    break;
                }
                $take = min($batch->quantity, $remaining);
                if ($take <= 0) {
                    continue;
                }

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
        return ! empty($cartItem->bundel_id);
    }

    private function findSelectedBundleItem($bundleItems, BundelDetails $detail, array $consumedSelectionKeys = [])
    {
        foreach ($bundleItems ?? [] as $selected) {
            $selectionKey = $this->buildSelectionKey($selected);

            if (! empty($consumedSelectionKeys) && in_array($selectionKey, $consumedSelectionKeys, true)) {
                continue;
            }

            if ((int) ($selected->product_id ?? 0) !== (int) $detail->product_id) {
                continue;
            }

            if (empty($selected->variant_id)) {
                if (empty($detail->selectedVariantIds())) {
                    return $selected;
                }

                continue;
            }

            if (in_array((string) $selected->variant_id, $detail->selectedVariantIds(), true)) {
                return $selected;
            }
        }

        return null;
    }

    private function buildSelectionKey($selected): string
    {
        if (! empty($selected->id)) {
            return 'id:'.$selected->id;
        }

        return 'product:'.($selected->product_id ?? 'null').':variant:'.($selected->variant_id ?? 'null');
    }

    private function getBundlePrice(CartItem $cartItem, Bundel $bundle): array
    {
        // can not use  getBundlePrice as this take first varaint price without checking user selection in cart
        // need to calculate price based on user selection in cart for each bundel detail
        $sale_price = 0;
        $price_after_discount = 0;
        $usedSelectionKeys = [];
        foreach ($bundle->bundelDetails as $d) {
            $selected = $this->findSelectedBundleItem($cartItem->cartBundelItems, $d, $usedSelectionKeys);
            if ($selected) {
                $usedSelectionKeys[] = $this->buildSelectionKey($selected);
            }

            if ($selected?->variant_id) {
                $allowedVariantIds = $d->selectedVariantIds();
                if (! in_array((string) $selected->variant_id, $allowedVariantIds, true)) {
                    throw new \Exception(__('main.invalid_bundle_selection'));
                }
                $v = $d->getVariants()->where('id', $selected->variant_id)->first();
                $sale_price += ($v->sale_price ?? 0) * ($d->quantity ?? 1);
                $price_after_discount += ($v->getDiscountPrice() ?? $v->sale_price ?? 0) * ($d->quantity ?? 1);
            } else {
                $p = $d->product;
                $sale_price += ($p->sale_price ?? 0) * ($d->quantity ?? 1);
                $price_after_discount += ($p->getDiscountPrice() ?? $p->sale_price ?? 0) * ($d->quantity ?? 1);
            }

        }

        if ($bundle->hasBundleDiscount()) {
            $price_after_discount = $bundle->applyBundleDiscount($sale_price);
        }

        return [$sale_price, $price_after_discount];

    }
}
