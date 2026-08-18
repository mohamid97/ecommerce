<?php

namespace App\Services\Ecommerce\Cart;

use App\Models\Api\Ecommerce\CartItem;

class CartPriceRefreshService
{
    public function __construct(private readonly CartRepository $cartRepository)
    {
    }

    /**
     * Promotion eligibility can affect any catalog item (not just an item
     * directly linked to the promotion), so recalculate every saved cart item.
     */
    public function refreshAll(): void
    {
        CartItem::query()
            ->with([
                'cartBundelItems',
                'bundel.bundelDetails',
            ])
            ->orderBy('id')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    $this->cartRepository->updateCartItemQuantity($item, (int) $item->quantity);
                }
            });
    }
}
