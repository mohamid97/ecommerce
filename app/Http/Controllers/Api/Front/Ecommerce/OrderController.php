<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Front\Order\AuthOrderStoreRequest;
use App\Http\Requests\Api\Front\Order\GuestOrderPreviewRequest;
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
                    'city',
                    'zone',
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
            $data = $request->only(['coupon_code', 'points', 'zone_id']);

            $result = $this->service->previewForUser($user, $data);

            $preview = [
                'subtotal' => (float) ($result['total_before_discount'] ?? 0),
                'coupon' => !empty($data['coupon_code']) ? [
                    'code' => (string) $data['coupon_code'],
                    'discount_amount' => (float) ($result['discount_amount'] ?? 0),
                ] : null,
                'points' => !empty($data['points']) ? [
                    'used' => (int) $data['points'],
                    'discount_amount' => (float) ($result['points_amount'] ?? 0),
                ] : null,
                'shipping' => [
                    'price' => (float) ($result['shipping_cost'] ?? 0),
                ],
                'total' => (float) ($result['total'] ?? 0),
                'errors' => [
                    'coupon' => null,
                    'points' => null,
                ],
            ];

            return $this->success($preview, __('main.retrieved_successfully' , ['model' => 'Order Preview']));
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
                if (!empty($item['bundle_id'])) {
                    $bundles[] = [
                        'bundle_id' => $item['bundle_id'],
                        'quantity' => $item['quantity'],
                        'bundle_items' => $item['bundle_items'] ?? [],
                    ];
                } else {
                    $products[] = [
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'] ?? null,
                        'quantity' => $item['quantity'],
                    ];
                    $this->service->checkMoqForGuest($item['product_id'], $item['variant_id'] ?? null, $item['quantity']);
                }
            }



            $cart = app(\App\Services\Ecommerce\Cart\CartService::class)->mapGuestCartData([
                'products' => $products,
                'bundles' => $bundles,
            ]);

            $order = $this->service->createOrderFromGuestCart($cart, $data);

            return $this->success(new OrderResource($order), __('main.created_successfully', ['model' => 'Order']));
        }catch(\Illuminate\Validation\ValidationException $e){
            throw $e;
        }catch(\Exception $e){
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Preview totals for a guest-provided cart payload (items + optional coupon).
     */
    public function previewGuest(GuestOrderPreviewRequest $request)
    {
        try {
            $data = $request->validated();

            $products = [];
            $bundles = [];

            foreach ($data['items'] as $item) {
                if (!empty($item['bundle_id'])) {
                    $bundles[] = [
                        'bundle_id' => $item['bundle_id'],
                        'quantity' => $item['quantity'],
                        'bundle_items' => $item['bundle_items'] ?? [],
                    ];
                } else {
                    $products[] = [
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'] ?? null,
                        'quantity' => $item['quantity'],
                    ];
                    $this->service->checkMoqForGuest($item['product_id'], $item['variant_id'] ?? null, $item['quantity']);
                }
            }

            $cart = app(\App\Services\Ecommerce\Cart\CartService::class)->mapGuestCartData([
                'products' => $products,
                'bundles' => $bundles,
            ]);

            $result = $this->service->calculateTotalsFromCart($cart, $data, null);

            $preview = [
                'subtotal' => (float) ($result['total_before_discount'] ?? 0),
                'coupon' => !empty($data['coupon_code']) ? [
                    'code' => (string) $data['coupon_code'],
                    'discount_amount' => (float) ($result['discount_amount'] ?? 0),
                ] : null,
                'points' => null,
                'shipping' => [
                    'price' => (float) ($result['shipping_cost'] ?? 0),
                ],
                'total' => (float) ($result['total'] ?? 0),
                'errors' => [
                    'coupon' => null,
                    'points' => null,
                ],
            ];

            return $this->success($preview, __('main.retrieved_successfully'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
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

            $order->load(['city', 'zone', 'items.product', 'items.variant', 'items.bundel.bundelDetails.product']);

            return $this->success(new OrderResource($order), __('main.retrieved_successfully', ['model' => 'Order']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }



}
