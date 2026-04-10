<?php
namespace App\DTO\Ecommerce\Cart;

class AddToCartDTO
{
    public int $product_id;
    public ?int $varaint_id = null;
    public int $quantity;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->product_id = (int) ($data['product_id'] ?? 0);
        $dto->varaint_id= isset($data['varaint_id']) ? (int) $data['varaint_id'] : null;
        $dto->quantity = (int) ($data['quantity'] ?? 1);

        return $dto;
    }
}
