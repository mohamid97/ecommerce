<?php
namespace App\DTO\Ecommerce\Promotion;

class UpdatePromotionDTO
{
    public function __construct(
        public int $id,
        public string $start_date,
        public string $end_date,
        public string $status,
        public bool $is_coupon,
        public ?string $coupon_code,
        public ?int $coupon_limit,
        public string $type,
        public float $type_value,
        public string $location,
        public string $target,
        public ?string $image,
        public ?array $categories,
        public ?int $product_id,
        public ?int $brand_id,
        public ?float $discount,
        public ?float $max_amount_discount,
        public array $title,
        public ?array $des,
        public ?array $meta_title,
        public ?array $meta_des,
        public ?string $customer_group = 'all',


    ) {}
    public static function fromRequest(array $data): self
    {
        return new self(
            $data['id'],
            $data['start_date'],
            $data['end_date'],
            $data['status'],
            $data['is_coupon'],
            $data['coupon_code'],
            $data['coupon_limit'] ?? null,
            $data['type'],
            $data['type_value'],
            $data['location'],
            $data['target'],
            $data['image'] ?? null,
            $data['categories'],
            $data['product_id'],
            $data['brand_id'],
            $data['discount'] ?? null,
            $data['max_amount_discount'] ?? null,
            $data['title'],
            $data['des'] ?? null,
            $data['meta_title'] ?? null,
            $data['meta_des'] ?? null,
            $data['customer_group'] ?? 'all',

        );
    }
}