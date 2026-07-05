<?php

namespace App\DTO\Ecommerce\Product;

class BulkAddStockDTO
{
    public function __construct(
        public int $product_id,
        public ?array $variant_ids,
        public float $quantity,
        public ?string $note,
        public ?float $cost_price,
        public float $sale_price,
        public ?string $status,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['product_id'],
            $data['variant_ids'] ?? null,
            (float) $data['quantity'],
            $data['note'] ?? null,
            isset($data['cost_price']) ? (float) $data['cost_price'] : null,
            (float) $data['sale_price'],
            $data['status'] ?? null,
        );
    }
}
