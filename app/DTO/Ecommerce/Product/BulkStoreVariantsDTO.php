<?php

namespace App\DTO\Ecommerce\Product;

class BulkStoreVariantsDTO
{
    public function __construct(
        public int $productId,
        public array $sharedData,
        public array $variants,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            (int) ($data['product_id'] ?? 0),
            $data['shared_data'] ?? [],
            $data['variants'] ?? [],
        );
    }
}
