<?php

namespace App\DTO\Ecommerce\Cart;

class AddToCartDTO
{
    public function __construct(
        public readonly int $product_id,
        public readonly ?int $product_option_id = null,
        public readonly int $quantity = 1
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            product_id: $data['product_id'],
            product_option_id: $data['product_option_id'] ?? null,
            quantity: $data['quantity'] ?? 1
        );
    }
}