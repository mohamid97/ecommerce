<?php

namespace App\Services\Admin\Ecommerce\Cart;

use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\CartItem;
use Exception;

class CartService
{
    /**
     * Get paginated carts list.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCarts(array $data)
    {
        $query = Cart::with(['items.product', 'items.variant', 'items.cartBundelItems']);

        if (!empty($data['user_id'])) {
            $query->where('user_id', $data['user_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * Show details of a single cart.
     *
     * @param  int  $id
     * @return \App\Models\Api\Ecommerce\Cart
     * @throws \Exception
     */
    public function getCartDetails($id)
    {
        $cart = Cart::with(['items.product', 'items.variant', 'items.cartBundelItems'])->find($id);

        if (!$cart) {
            throw new Exception('Cart not found');
        }

        return $cart;
    }

    /**
     * Delete a single cart by its ID.
     *
     * @param  int  $id
     * @return bool
     * @throws \Exception
     */
    public function deleteCart($id)
    {
        $deleted = Cart::where('id', $id)->delete();

        if (!$deleted) {
            throw new Exception('Cart not found');
        }

        return true;
    }

    /**
     * Delete a cart item by its ID.
     *
     * @param  int  $item_id
     * @return bool
     * @throws \Exception
     */
    public function deleteCartItem($item_id)
    {
        $deleted = CartItem::where('id', $item_id)->delete();

        if (!$deleted) {
            throw new Exception('Cart item not found');
        }

        return true;
    }

    /**
     * Clear all carts for a given user ID.
     *
     * @param  int  $user_id
     * @return void
     */
    public function clearUserCarts($user_id)
    {
        Cart::where('user_id', $user_id)->delete();
    }
}
