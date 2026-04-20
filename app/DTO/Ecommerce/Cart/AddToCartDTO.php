<?php

namespace App\DTO\Ecommerce\Cart;

class AddToCartDTO
{
    /** Always required — present in all three strategies. */
    public int $product_id;

    /** Optional — only sent when the product has options/variants. */
    public ?int $variant_id = null;

    /** Optional — triggers the BundleStrategy when present. */
    public ?int $bundel_id = null;

    public int $quantity = 1;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->product_id = (int) $data['product_id'];                                   // always present (request validates this)
        $dto->variant_id = isset($data['variant_id']) ? (int) $data['variant_id'] : null;
        $dto->bundel_id  = isset($data['bundel_id'])  ? (int) $data['bundel_id']  : null;
        $dto->quantity   = (int) ($data['quantity'] ?? 1);

        return $dto;
    }
}
