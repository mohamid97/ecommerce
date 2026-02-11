<?php
namespace App\DTO\Ecommerce\Product;

class StoreVaraintDTO
{
    public function __construct(
        public int $product_id,
        public array $optionValueIds,
        public ?float $salePrice = null,
        public ?int $stock = null,
        public ?string $barcode = null,
        public ?array $title = null,
        public ?array $des = null,
        public ?float $discount = null,
        public ?string $discountType = null,
        public ?float $length = null,
        public ?float $weight = null,
        public ?float $width = null,
        public ?float $height = null,
        //delivery_time , max_time , images
        public int $deliveryTime = 0,
        public int $maxTime = 0,
        public ?array $images = null,
        public ?string $sku = null,
        public ?array $metaTitle= null,
        public ?array $metaDes = null,
        
    
    ) {}

    public static function fromRequest(array $data): self
    {

        return new self(
            $data['product_id'],
            $data['option_value_ids'],
            $data['sale_price'] ?? null,
            $data['stock'] ?? null,
            $data['barcode'] ?? null,
            $data['title'] ?? null,
            $data['des'] ?? null,
            $data['discount'] ?? null,
            $data['discount_type'] ?? null,
            $data['length'] ?? null,
            $data['weight'] ?? null,
            $data['width'] ?? null,
            $data['height'] ?? null,
            $data['delivery_time'] ?? 0,
            $data['max_time'] ?? 0,
            $data['images'] ?? null,
            $data['sku'] ?? null,
            $data['meta_title'] ?? null,
            $data['meta_des'] ?? null,
            
        );
    }
}