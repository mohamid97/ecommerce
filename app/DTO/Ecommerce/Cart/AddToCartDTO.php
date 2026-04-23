<?php

namespace App\DTO\Ecommerce\Cart;

class AddToCartDTO
{
    public ?int $product_id = null;
    public ?int $variant_id = null;
    public ?int $bundel_id = null;
    public int $quantity = 1;
    public array $bundle_items = [];

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->product_id   = isset($data['product_id']) ? (int) $data['product_id'] : null;
        $dto->variant_id   = isset($data['variant_id']) ? (int) $data['variant_id'] : null;
        $dto->bundel_id    = isset($data['bundel_id'])  ? (int) $data['bundel_id']  : null;
        $dto->quantity     = (int) ($data['quantity'] ?? 1);
        $dto->bundle_items = $data['bundle_items'] ?? [];

        return $dto;
    }
}
