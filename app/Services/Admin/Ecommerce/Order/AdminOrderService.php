<?php

namespace App\Services\Admin\Ecommerce\Order;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\Order as OrderModel;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\StockMovment;
use App\Models\Api\Ecommerce\Promotion;
use App\Models\User;
use App\Services\Ecommerce\Order\OrderService as EcommerceOrderService;
use App\Services\Ecommerce\Order\PointsService;
use App\Services\Admin\User\UserService;
use Illuminate\Support\Facades\DB;

class AdminOrderService
{
    public function __construct(
        protected AdminOrderRepository $repository,
        protected EcommerceOrderService $orderService
    ) {}

    /**
     * Return paginated orders according to admin filters.
     */
    public function listOrders(array $filters)
    {
        return $this->repository->paginateOrders($filters);
    }

    /**
     * View single order with relations.
     */
    public function viewOrder(array $data): OrderModel
    {
        $query = OrderModel::with([
            'user',
            'government',
            'items.batches.stockMovment',
            'items.product',
            'items.variant.variants.optionValue.option',
            'items.orderBundelItems.product',
            'items.orderBundelItems.variant.variants.optionValue.option',
            'items.bundel.bundelDetails.product',
        ]);

        return !empty($data['order_number'])
            ? $query->where('order_number', $data['order_number'])->firstOrFail()
            : $query->findOrFail($data['id']);
    }

    /**
     * Return order summary for a given user.
     */
    public function userOrderSummary(int $userId)
    {
        return app(UserService::class)->orderSummary($userId);
    }

    /**
     * Update order status and payment status.
     */
    public function updateStatus(array $data): OrderModel
    {
        return DB::transaction(function () use ($data) {
            $order = $this->repository->findByIdOrNumber($data);
            $wasEverDelivered = !is_null($order->delivered_at);
            $wasRefunded = $order->status === 'refunded';

            $order->status = $data['status'];
            $order->payment_status = $data['payment_status'];

            if ($order->status === 'delivered' && !$order->delivered_at) {
                $order->delivered_at = now();
            }

            $this->repository->save($order);

            // Increase sales counters once, the first time an order reaches delivered.
            if (!$wasEverDelivered && $order->status === 'delivered') {
                $this->increaseSalesNumbersForDeliveredOrder($order);
            }

            // Restore stock when an order is refunded.
            if (!$wasRefunded && $order->status === 'refunded') {
                $this->restoreStockForRefundedOrder($order);
            }

            // award points if necessary
            app(PointsService::class)->awardPointsForCompletedPaidOrder($order);

            return $order->fresh('user');
        });
    }

    /**
     * Permanently delete an order while reversing every effect created by it.
     * Refunded orders have already had their stock restored, so that step is
     * intentionally skipped for them.
     */
    public function deleteOrder(array $data): void
    {
        DB::transaction(function () use ($data) {
            $order = $this->repository->findForDeletion($data);

            if ($order->status !== 'refunded') {
                $this->restoreStockForRefundedOrder($order);
            }

            if ($order->delivered_at) {
                $this->decreaseSalesNumbersForDeletedOrder($order);
            }

            $this->reversePointsForDeletedOrder($order);
            $this->releaseCouponUsageForDeletedOrder($order);

            // order_items, order_item_batches, and order_item_bundel_items
            // are removed by their database cascade constraints.
            $this->repository->delete($order);
        });
    }

    /**
     * Create a guest order from admin UI payload.
     * Accepts the same payload shape as front GuestOrderStoreRequest.
     */
    public function createGuestOrder(array $data): OrderModel
    {
        $products = [];
        $bundles = [];

        foreach ($data['items'] as $item) {
            if (!empty($item['bundel_id'])) {
                $bundles[] = [
                    'bundle_id' => $item['bundel_id'],
                    'quantity' => $item['quantity'],
                    'bundle_items' => $item['bundle_items'] ?? [],
                ];
            } else {
                $products[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                ];
            }
        }

        $cart = app(\App\Services\Ecommerce\Cart\CartService::class)->mapGuestCartData([
            'products' => $products,
            'bundles' => $bundles,
        ]);

        $order = $this->orderService->createOrderFromGuestCart($cart, $data);

        $this->repository->attachAdminMeta($order, []);

        return $order;
    }

    private function increaseSalesNumbersForDeliveredOrder(OrderModel $order): void
    {
        $order->load([
            'items.orderBundelItems',
        ]);

        foreach ($order->items as $item) {
            $orderItemQty = (int) ($item->quantity ?? 0);

            if ($orderItemQty <= 0) {
                continue;
            }

            // Bundle order item:
            // - increment bundle sales by number of sold bundles (order item quantity)
            // - increment underlying products/variants by bundle component qty * bundle qty
            if (!empty($item->bundel_id)) {
                Bundel::whereKey($item->bundel_id)->increment('sales_number', $orderItemQty);

                if ($item->orderBundelItems->isNotEmpty()) {
                    foreach ($item->orderBundelItems as $bundleItem) {
                        $perBundleQty = (int) ($bundleItem->quantity ?? 0);
                        $salesQty = $orderItemQty * max($perBundleQty, 0);

                        if ($salesQty <= 0) {
                            continue;
                        }

                        if (!empty($bundleItem->variant_id)) {
                            ProductVariant::whereKey($bundleItem->variant_id)->increment('sales_number', $salesQty);
                            continue;
                        }

                        if (!empty($bundleItem->product_id)) {
                            Product::whereKey($bundleItem->product_id)->increment('sales_number', $salesQty);
                        }
                    }

                    continue;
                }

                // Fallback for old records missing order_item_bundel_items.
                $snapshotDetails = (array) data_get($item->bundel_snapshot, 'details', []);

                foreach ($snapshotDetails as $detail) {
                    $perBundleQty = (int) ($detail['quantity'] ?? 0);
                    $salesQty = $orderItemQty * max($perBundleQty, 0);

                    if ($salesQty <= 0) {
                        continue;
                    }

                    $selectedVariantId = $detail['selected_variant_id'] ?? null;
                    if (!empty($selectedVariantId)) {
                        ProductVariant::whereKey($selectedVariantId)->increment('sales_number', $salesQty);
                        continue;
                    }

                    $productId = $detail['product_id'] ?? null;
                    if (!empty($productId)) {
                        Product::whereKey($productId)->increment('sales_number', $salesQty);
                    }
                }

                continue;
            }

            // Variant order item
            if (!empty($item->variant_id)) {
                ProductVariant::whereKey($item->variant_id)->increment('sales_number', $orderItemQty);
                continue;
            }

            // Simple product order item
            if (!empty($item->product_id)) {
                Product::whereKey($item->product_id)->increment('sales_number', $orderItemQty);
            }
        }
    }

    private function decreaseSalesNumbersForDeletedOrder(OrderModel $order): void
    {
        foreach ($order->items as $item) {
            $orderItemQty = (int) ($item->quantity ?? 0);

            if ($orderItemQty <= 0) {
                continue;
            }

            if (!empty($item->bundel_id)) {
                $this->decreaseSalesNumber(Bundel::class, $item->bundel_id, $orderItemQty);

                if ($item->orderBundelItems->isNotEmpty()) {
                    foreach ($item->orderBundelItems as $bundleItem) {
                        $salesQty = $orderItemQty * max((int) ($bundleItem->quantity ?? 0), 0);

                        if (!empty($bundleItem->variant_id)) {
                            $this->decreaseSalesNumber(ProductVariant::class, $bundleItem->variant_id, $salesQty);
                        } elseif (!empty($bundleItem->product_id)) {
                            $this->decreaseSalesNumber(Product::class, $bundleItem->product_id, $salesQty);
                        }
                    }

                    continue;
                }

                foreach ((array) data_get($item->bundel_snapshot, 'details', []) as $detail) {
                    $salesQty = $orderItemQty * max((int) ($detail['quantity'] ?? 0), 0);
                    $variantId = $detail['selected_variant_id'] ?? null;

                    if ($variantId) {
                        $this->decreaseSalesNumber(ProductVariant::class, $variantId, $salesQty);
                    } elseif (!empty($detail['product_id'])) {
                        $this->decreaseSalesNumber(Product::class, $detail['product_id'], $salesQty);
                    }
                }

                continue;
            }

            if (!empty($item->variant_id)) {
                $this->decreaseSalesNumber(ProductVariant::class, $item->variant_id, $orderItemQty);
            } elseif (!empty($item->product_id)) {
                $this->decreaseSalesNumber(Product::class, $item->product_id, $orderItemQty);
            }
        }
    }

    private function decreaseSalesNumber(string $model, int $id, int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $record = $model::query()->lockForUpdate()->find($id);

        if (!$record) {
            return;
        }

        $record->sales_number = max(0, (int) ($record->sales_number ?? 0) - $quantity);
        $record->save();
    }

    private function reversePointsForDeletedOrder(OrderModel $order): void
    {
        if (!$order->user_id) {
            return;
        }

        $user = User::query()->lockForUpdate()->find($order->user_id);

        if (!$user) {
            return;
        }

        $user->points = max(
            0,
            (int) ($user->points ?? 0)
                + (int) ($order->points_used ?? 0)
                - (int) ($order->points_earned ?? 0)
        );
        $user->save();
    }

    private function releaseCouponUsageForDeletedOrder(OrderModel $order): void
    {
        if (empty($order->coupon_code)) {
            return;
        }

        $promotion = Promotion::query()
            ->where('is_coupon', true)
            ->where('coupon_code', $order->coupon_code)
            ->lockForUpdate()
            ->first();

        if ($promotion && $promotion->coupon_limit !== null) {
            $promotion->increment('coupon_limit');
        }
    }


    /**
     * Reverse every stock batch that was consumed when the order was created.
     *
     * For each order_item_batch row we:
     *   1. Add the taken quantity back to the original stock_movment row.
     *   2. Add the same quantity back to the denormalized product/variant stock counter.
     */
    private function restoreStockForRefundedOrder(OrderModel $order): void
    {
        // Load items → batches → the original stock movement in one query.
        $order->load([
            'items.batches.stockMovment.variant',
            'items.batches.stockMovment.product',
        ]);

        foreach ($order->items as $item) {
            foreach ($item->batches as $batch) {
                $movement = $batch->stockMovment;

                if (!$movement) {
                    continue; // safety – orphaned record, skip
                }

                // 1. Restore the stock_movment quantity.
                $movement->quantity += $batch->quantity;
                $movement->save();

                // 2. Keep the denormalized stock counter in sync.
                if ($movement->variant_id && $movement->variant) {
                    $movement->variant->stock += $batch->quantity;
                    $movement->variant->save();
                } elseif ($movement->product_id && $movement->product) {
                    $movement->product->stock += $batch->quantity;
                    $movement->product->save();
                }
            }
        }
    }
}
