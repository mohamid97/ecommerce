<?php
namespace App\DTO\Ecommerce\Cart;


class RemoveFromCartDTO
{
    public function __construct(
        public readonly ?int $cartItemId = null,
        public readonly ?int $productId = null,
        public readonly ?int $variantId = null,
        public readonly ?int $bundelId = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            cartItemId: $data['cart_item_id'] ?? null,
            productId: $data['product_id'] ?? null,
            variantId: $data['variant_id'] ?? null,
            bundelId: $data['bundel_id'] ?? null,
        );
    }
}