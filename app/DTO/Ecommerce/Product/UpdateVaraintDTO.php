<?php
namespace App\DTO\Ecommerce\Product;

class UpdateVaraintDTO
{
    public function __construct(
        public int $product_id,
        public array $optionValueIds,
        public string $sku,
        public float $price,
        public int $stock,
        public ?string $barcode = null,
        public ?array $titles = null,
        public ?array $des = null,
        public ?array $meta_title = null,
        public ?array $meta_des = null,
        public float $cost_price = 0,
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
            $data['product_id'],
            $data['option_value_ids'],
            $data['sku'],
            $data['price'],
            $data['stock'],
            $data['barcode'] ?? null,
            $data['title'] ?? null,
            $data['des'] ?? null,
            $data['meta_title'] ?? null,
            $data['meta_des'] ?? null,
            $data['cost_price'] ?? 0,
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

