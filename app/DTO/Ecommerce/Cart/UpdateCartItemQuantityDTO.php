<?php

namespace App\DTO\Ecommerce\Cart;

class UpdateCartItemQuantityDTO
{
    public function __construct(
        public readonly int $cartItemId,
        public readonly int $quantity,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            cartItemId: (int) $data['cart_item_id'],
            quantity: (int) $data['quantity'],
        );
    }
}
