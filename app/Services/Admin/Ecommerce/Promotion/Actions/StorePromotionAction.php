<?php

namespace App\Services\Admin\Ecommerce\Promotion\Actions;

use App\Models\Api\Ecommerce\Promotion;
use App\Services\Admin\Common\TranslationService;

class StorePromotionAction
{
    public function __construct(
        private readonly TranslationService $translation,
    ) {}

    public function execute($data)
    {
        $data->is_coupon = filter_var($data->is_coupon, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? 1 : 0;

        $promotion = Promotion::create([
            'start_date' => $data->start_date,
            'end_date' => $data->end_date,
            'status' => $data->status,
            'is_coupon' => $data->is_coupon,
            'coupon_code' => $data->coupon_code ?? null,
            'coupon_limit' => $data->coupon_limit ?? null,
            'type' => $data->type,
            'location' => $data->location,
            'target' => $data->target,
            'image' => $data->image ?? null,
            'product_id' => $data->product_id ?? null,
            'customer_group' => $data->customer_group ?? 'all',
            'discount' => $data->discount ?? null,
            'max_amount_discount' => $data->max_amount_discount ?? null,
            'bundel_id' => $data->bundel_id ?? null,
        ]);

        // Sync brands and categories
        if (! empty($data->brands)) {
            $promotion->brands()->sync($data->brands);
        }

        if (! empty($data->categories)) {
            $promotion->categories()->sync($data->categories);
        }

        $this->translation->storeTranslations($promotion, $data, ['title', 'des', 'meta_title', 'meta_des']);

        return $promotion->load('brands', 'categories');
    }
}
