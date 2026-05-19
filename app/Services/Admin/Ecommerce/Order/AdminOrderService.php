<?php

namespace App\Services\Admin\Ecommerce\Order;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\Order as OrderModel;
use App\Models\Api\Ecommerce\ProductVariant;
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

            // award points if necessary
            app(PointsService::class)->awardPointsForCompletedPaidOrder($order);

            return $order->fresh('user');
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

    
}
