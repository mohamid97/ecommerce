<?php

namespace App\Services\Ecommerce\Cart;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\CartItem;

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
    public function createOrUpdateCard(int $userId, AddToCartDTO $dto): Cart
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        // ── Bundle: product comes from inside a bundle ──────────────────────
        if (isset($dto->bundel_id)) {
            $matchKey   = [
                'cart_id'    => $cart->id,
                'bundel_id'  => $dto->bundel_id,
                'product_id' => $dto->product_id,
                'variant_id' => $dto->variant_id ?? null,   // null when no variant sent
            ];
            $attributes = [
                'quantity'              => $dto->quantity,
                'total_before_discount' => 0,
                'total_after_discount'  => 0,
            ];

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
                'total_before_discount' => 0,
                'total_after_discount'  => 0,
            ];

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
                'total_before_discount' => 0,
                'total_after_discount'  => 0,
            ];
        }

        CartItem::updateOrCreate($matchKey, $attributes);

        return $cart;
    }

    // ───────────────────────────────────────────────────────────────────────

    public function removeFromCart(int $userId, $dto): void
    {
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return;
        }

        CartItem::where('cart_id', $cart->id)
            ->where('product_id', $dto->productId)
            ->when($dto->variantId, fn ($q) => $q->where('variant_id', $dto->variantId))
            ->delete();

        // Delete the whole cart when it becomes empty
        if ($cart->items()->count() === 0) {
            $cart->delete();
        }
    }
}