<?php
namespace App\DTO\Ecommerce\Product;

class UpdateStockDTO
{

    // variant id can be null or not sent
    public function __construct(
        public int $id,
        public int $product_id,
        public ?int $variant_id,
        public int $quantity,
        public string $type,
        public ?string $note,
        public ?float $cost_price,
        public float $sales_price,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['id'],
            $data['product_id'],
            $data['variant_id'] ?? null,
            $data['quantity'],
            $data['type'], 
            $data['note'] ?? null,
            $data['cost_price'] ?? null,
            $data['sales_price'],
        );
    }
}