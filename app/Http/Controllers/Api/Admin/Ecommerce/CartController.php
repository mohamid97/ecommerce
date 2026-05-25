<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Admin\Ecommerce\CartResource;
use App\Http\Resources\Api\Admin\Ecommerce\CartListResource;
use App\Services\Admin\Ecommerce\Cart\CartService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ResponseTrait;

    public function __construct(private CartService $service) {}

    /**
     * List carts (paginated).
     */
    public function index(Request $request)
    {
        try {
            $paginator = $this->service->getCarts($request->all());
            $resourceCollection = CartListResource::collection($paginator->getCollection());
            
            return $this->successPaginated($paginator, $resourceCollection, 'carts', __('main.retrieved_successfully', ['model' => 'carts']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Show a single cart with items.
     */
    public function show(Request $request)
    {
        try {
            $cart = $this->service->getCartDetails($request->id);
            
            return $this->success(new CartResource($cart), __('main.retrieved_successfully', ['model' => 'cart']));
        } catch (\Exception $e) {
            return $this->success(null, 'Cart not found');
        }
    }

    /**
     * Delete a cart by id.
     */
    public function deleteAll(Request $request)
    {
        try {
            $this->service->deleteCart($request->id);
            
            return $this->success(null, 'Cart deleted');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 404);
        }
    }

    /**
     * Delete a cart item by id.
     */
    public function deleteItem(Request $request)
    {
        try {
            $this->service->deleteCartItem($request->item_id);
            
            return $this->success(null, __('main.deleted_successfully', ['model' => 'cart item']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 404);
        }
    }

    /**
     * Clear all carts for a given user id.
     */
    public function clearByUser(Request $request)
    {
        try {
            $this->service->clearUserCarts($request->user_id);
            
            return $this->success(null, __('main.cleared_successfully', ['model' => 'user carts']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}