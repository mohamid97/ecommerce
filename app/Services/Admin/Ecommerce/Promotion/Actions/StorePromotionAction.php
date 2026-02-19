<?php
namespace App\Services\Admin\Ecommerce\Promotion\Actions;

use App\Models\Api\Ecommerce\Promotion;
use App\Services\Admin\Common\TranslationService;

class StorePromotionAction
{
    public function __construct(
        private readonly TranslationService $translation,
    ) {}

    public function execute(array $data)
    {
        $promotion = Promotion::create([
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
            'is_coupon' => $data['is_coupon'],
            'coupon_code' => $data['coupon_code'],
            'coupon_limit' => $data['coupon_limit'],
            'type' => $data['type'],
            'type_value' => $data['type_value'],
            'location' => $data['location'],
            'target' => $data['target'],
            'image' => $data['image'] ?? null,
            'categories' => $data['categories'] ?? null,
            'product_id' => $data['product_id'] ?? null,
            'brand_id' => $data['brand_id'] ?? null,
            'customer_group' => $data['customer_group'] ?? 'all',
            'discount' => $data['discount'] ?? null,
            'max_amount_discount' => $data['max_amount_discount'] ?? null,
        ]);

        $this->translation->storeTranslations($promotion, $data , ['title' , 'des' , 'meta_title' , 'meta_des']);
        return $promotion;
    }

}