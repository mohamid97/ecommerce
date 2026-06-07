<?php
namespace App\DTO\Ecommerce\Product;

class UpdateVaraintDTO
{
    public function __construct(
        public int $id,
        public ?string $sku = null,
        public ?float $sale_price = null,
        public ?int $stock,
        public ?string $barcode = null,
        public ?array $title = null,
        public ?array $slug = null,
        public ?array $des = null,
        public ?array $meta_title = null,
        public ?array $meta_des = null,
        public ?float $discount = null,
        public ?string $discount_type = null,
        public ?float $length = null,
        public ?float $weight = null,
        public ?float $width = null,
        public ?float $height = null,
        public int $delivery_time = 0,
        public int $max_time = 0,
        public ?array $images = null,

        

    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['id'],
            $data['sku'] ?? null,
            $data['sale_price'] ?? null,
            $data['stock'] ?? null,
            $data['barcode'] ?? null,
            $data['title'] ?? null,
            $data['slug'] ?? null,
            $data['des'] ?? null,
            $data['meta_title'] ?? null,
            $data['meta_des'] ?? null,
            $data['discount'] ?? null,
            $data['discount_type'] ?? null,
            $data['length'] ?? null,
            $data['weight'] ?? null,
            $data['width'] ?? null,
            $data['height'] ?? null,
            $data['delivery_time'] ?? 0,
            $data['max_time'] ?? 0,
            $data['images'] ?? null,
            
        );
    }
}       
