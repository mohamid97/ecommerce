<?php

namespace App\Services\Ecommerce\Cart;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\ProductVariant;

class CartRepository
{
    /**
     * Create or update a cart item.
     *
     * Match keys per strategy:
     *   BundleStrategy            → cart_id + bundel_id + product_id + variant_id (nullable)
     *   ProductWithOptionStrategy → cart_id + product_id + variant_id
     *   SimpleProductStrategy     → cart_id + product_id (variant_id IS NULL, bundel_id IS NULL)
     */
    public function createOrUpdateCard(int $userId, AddToCartDTO $dto ,$priceData = null): Cart
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        // ── Bundle: product comes from inside a bundle ──────────────────────
        if (isset($dto->bundel_id)) {
            $matchKey   = [
                'cart_id'    => $cart->id,
                'bundel_id'  => $dto->bundel_id,
            ];
            $attributes = [
                'product_id'            => null,
                'variant_id'            => null,
                'quantity'              => $dto->quantity,
                'type'                  =>'bundle',
                'total_before_discount' => $priceData['total_price'] * $dto->quantity,
                'total_after_discount'  => $priceData['total_discount_price'] * $dto->quantity,
            ];

            $cartItem = CartItem::updateOrCreate($matchKey, $attributes);

            // Resync bundle items
            $cartItem->cartBundelItems()->delete();
            $bundleItemRows = [];
            foreach ($dto->bundle_items as $item) {
                $bundleItemRows[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                ];
            }
            $cartItem->cartBundelItems()->createMany($bundleItemRows);

        // ── Product + Variant ───────────────────────────────────────────────
        } elseif (isset($dto->variant_id)) {
            $matchKey   = [
                'cart_id'    => $cart->id,
                'product_id' => $dto->product_id,
                'variant_id' => $dto->variant_id,
            ];
            $attributes = [
                'quantity'              => $dto->quantity,
                'bundel_id'             => null,
                'type'                  =>'variant',
                'total_before_discount' => ProductVariant::find($dto->variant_id)->sale_price * $dto->quantity,
                'total_after_discount'  => ProductVariant::find($dto->variant_id)->getDiscountedPrice() * $dto->quantity,
            ];

            CartItem::updateOrCreate($matchKey, $attributes);

        // ── Simple product (no variant, no bundle) ──────────────────────────
        } else {
            $matchKey   = [
                'cart_id'    => $cart->id,
                'product_id' => $dto->product_id,
                'variant_id' => null,
                'bundel_id'  => null,
            ];
            $attributes = [
                'quantity'              => $dto->quantity,
                'type'                  =>'product',
                'total_before_discount' => Product::find($dto->product_id)->sale_price * $dto->quantity,
                'total_after_discount'  => Product::find($dto->product_id)->getDiscountedPrice() * $dto->quantity,
            ];

            CartItem::updateOrCreate($matchKey, $attributes);
        }

        return $cart;
    }

    // ───────────────────────────────────────────────────────────────────────

    public function removeFromCart(int $userId, $dto): void
    {
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return;
        }

        if (isset($dto->bundelId)) {
            CartItem::where('cart_id', $cart->id)
                ->where('bundel_id', $dto->bundelId)
                ->delete();
        } else {
            CartItem::where('cart_id', $cart->id)
                ->where('product_id', $dto->productId)
                ->when($dto->variantId, fn ($q) => $q->where('variant_id', $dto->variantId))
                ->delete();
        }

        // Delete the whole cart when it becomes empty
        if ($cart->items()->count() === 0) {
            $cart->delete();
        }
    }
}