<?php

namespace App\Services\Ecommerce\Order;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\Cart;

use Exception;

class OrderAction
{
    public function checkProductExists(int $id): void
    {
        if (!Product::find($id)) {
            throw new \RuntimeException(__('main.product_not_found'));
        }
    }

    public function checkVariantExists(int $id): void
    {
        if (!ProductVariant::find($id)) {
            throw new \RuntimeException(__('main.variant_not_found'));
        }
    }

    public function checkBundelExists(int $id): void
    {
        if (!Bundel::find($id)) {
            throw new \RuntimeException(__('main.bundle_not_found'));
        }
    }

    /**
     * Return the open cart for a user or throw if missing/empty.
     *
     * @param int $userId
     * @return Cart
     * @throws Exception
     */
    public function getUserOpenCart(int $userId): Cart
    {
        $cart = Cart::with('items.cartBundelItems.variant','items.variant','items.product')
                ->where('user_id', $userId)->where('status', 'open')->first();

        if (!$cart) {
            throw new \Exception(__('main.user_cart_not_found'));
        }

        if ($cart->items->isEmpty()) {
            throw new \Exception(__('main.empty_cart'));
        }

        return $cart;
    }





    public function deleteCart(int $userId): void
    {
        Cart::where('user_id', $userId)->delete();
    }
}
