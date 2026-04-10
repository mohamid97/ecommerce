<?php
namespace App\DTO\Ecommerce\Cart;


class RemoveFromCartDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly ?int $variantId = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            productId: $data['product_id'],
            variantId: $data['variant_id'] ?? null,
        );
    }
}