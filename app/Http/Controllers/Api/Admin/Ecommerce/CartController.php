<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Admin\Ecommerce\CartResource;
use App\Http\Resources\Api\Admin\Ecommerce\CartListResource;
use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\CartItem;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ResponseTrait;

    /**
     * List carts (paginated).
     */
    public function index(Request $request)
    {
        $query = Cart::with(['items.product', 'items.variant', 'items.cartBundelItems']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $paginator = $query->orderBy('created_at', 'desc')->paginate(15);

        $resourceCollection = CartListResource::collection($paginator->getCollection());

        return $this->successPaginated($paginator, $resourceCollection, 'carts', __('main.retrieved_successfully' , ['model'=>'carts']));
    }

    /**
     * Show a single cart with items.
     */
    public function show(Request $request)
    {
        $cart = Cart::with(['items.product', 'items.variant', 'items.cartBundelItems'])->find($request->id);

        if (!$cart) {
            return $this->success(null, 'Cart not found');
        }

        return $this->success(new CartResource($cart), __('main.retrieved_successfully' , ['model'=>'cart']));
    }

    /**
     * Delete a cart by id.
     */
    public function deleteAll(Request $request)
    {
        $deleted = Cart::where('id', $request->id)->delete();

        if (! $deleted) {
            return $this->error('Cart not found', 404);
        }

        return $this->success(null, 'Cart deleted');
    }

    public function deleteItem(Request $request)
    {
        $deleted = CartItem::where('id', $request->item_id)->delete();

        if (! $deleted) {
            return $this->error('Cart item not found', 404);
        }

        return $this->success(null, __('main.deleted_successfully', ['model' => 'cart item']));
    }

    /**
     * Clear all carts for a given user id.
     */
    public function clearByUser(Request $request)
    {
        Cart::where('user_id', $request->user_id)->delete();

        return $this->success(null, __('main.cleared_successfully', ['model' => 'user carts']));
    }

}