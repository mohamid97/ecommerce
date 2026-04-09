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
        public string $location,
        public string $target,
        public ?string $image,
        public ?array $categories,
        public ?int $product_id,
        public ?array $brands,
        public ?float $discount,
        public ?float $max_amount_discount,
        public array $title,
        public ?array $des,
        public ?array $meta_title,
        public ?array $meta_des,
        public ?string $customer_group = 'all',
        public ?int $bundel_id = null


    ) {}
    public static function fromRequest(array $data): self
    {
        return new self(
            $data['id'],
            $data['start_date'],
            $data['end_date'],
            $data['status'],
            $data['is_coupon']??false,
            $data['coupon_code']?? null,
            $data['coupon_limit'] ?? null,
            $data['type'] ??'fixed',
            $data['location'],
            $data['target']??'hero',
            $data['image'] ?? null,
            $data['categories'],
            $data['product_id']?? null,
            $data['brands'] ?? [],
            $data['discount'] ?? null,
            $data['max_amount_discount'] ?? null,
            $data['title'],
            $data['des'] ?? null,
            $data['meta_title'] ?? null,
            $data['meta_des'] ?? null,
            $data['customer_group'] ?? 'all',
            $data['bundel_id'] ?? null

        );
    }
    
}