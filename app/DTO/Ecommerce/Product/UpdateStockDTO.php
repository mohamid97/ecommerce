<?php
namespace App\DTO\Ecommerce\Product;

class UpdateStockDTO
{

    // variant id can be null or not sent
    public function __construct(
        public int $id,
        public int $quantity,
        public ?string $note,
        public ?float $cost_price,
        public float $sale_price,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['id'],
            $data['quantity'],
            $data['note'] ?? null,
            $data['cost_price'] ?? null,
            $data['sale_price'],
        );
    }
}