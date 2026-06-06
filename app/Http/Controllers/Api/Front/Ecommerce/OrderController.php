<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Front\Order\AuthOrderStoreRequest;
use App\Http\Requests\Api\Front\Order\GuestOrderStoreRequest;
use App\Http\Resources\Api\Front\Ecommerce\OrderResource;
use App\Services\Ecommerce\Order\OrderService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ResponseTrait;
    public function __construct(protected OrderService $service) {}

    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $perPage = (int) $request->get('per_page', 10);

            $paginator = \App\Models\Api\Ecommerce\Order::query()
                ->where('user_id', $user->id)
                ->with([
                    'government',
                    'items.product',
                    'items.variant.variants.optionValue.option',
                    'items.bundel.bundelDetails.product',
                    'items.orderBundelItems.product',
                    'items.orderBundelItems.variant.variants.optionValue.option',
                ])
                ->withCount('items')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $collection = \App\Http\Resources\Api\Front\Ecommerce\OrderListResource::collection($paginator->getCollection());

            return $this->successPaginated($paginator, $collection, 'orders', __('main.retrieved_successfully', ['model' => 'Orders']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function store(AuthOrderStoreRequest $request)
    {
        try{
            $user = $request->user();
            $order = $this->service->createOrderFromCart($user, $request->validated());
            return $this->success(new OrderResource($order), __('main.created_successfully', ['model' => 'Order']));
        }catch(\Exception $e){
            return $this->error($e->getMessage(), 400);
        }
        
    }

    /**
     * Preview totals for authenticated user's current cart with optional coupon/points.
     */
    public function preview(Request $request)
    {
        try {
            $user = $request->user();
            $data = $request->only(['coupon_code', 'use_points', 'points_to_use']);

            $result = $this->service->previewForUser($user, $data);

            return $this->success($result, __('main.retrieved_successfully'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function storeGuest(GuestOrderStoreRequest $request)
    {
        try{
            $data = $request->validated();

            // normalize incoming items into products/bundles arrays for CartService
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

            $order = $this->service->createOrderFromGuestCart($cart, $data);

            return $this->success(new OrderResource($order), __('main.created_successfully', ['model' => 'Order']));
        }catch(\Exception $e){
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Preview totals for a guest-provided cart payload (items + optional coupon).
     */
    public function previewGuest(Request $request)
    {
        try {
            $data = $request->validate([
                'items' => 'required|array',
                'coupon_code' => 'nullable|string',
            ]);

            // normalize incoming items into products/bundles arrays for CartService
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

            $result = $this->service->calculateTotalsFromCart($cart, $data, null);

            return $this->success($result, __('main.retrieved_successfully'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function show(Request $request, string $orderNumber)
    {
        try {
            $user = $request->user();

            $order = \App\Models\Api\Ecommerce\Order::where('order_number', $orderNumber)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return $this->error(__('main.not_found', ['model' => 'Order']), 404);
            }

            $order->load(['government', 'items.product', 'items.variant', 'items.bundel.bundelDetails.product']);

            return $this->success(new OrderResource($order), __('main.retrieved_successfully', ['model' => 'Order']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }



}
