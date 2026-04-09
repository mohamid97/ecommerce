<?php
namespace App\Services\Admin\Ecommerce\Promotion\Actions;

use App\Models\Api\Ecommerce\Promotion;
use App\Services\Admin\Common\TranslationService;

class UpdatePromotionAction
{
    public function __construct(
        private readonly TranslationService $translation,
    ) {}

    public function execute( $data)
    {
        $promotion = Promotion::findOrFail($data['id']);
        $data->is_coupon =  filter_var($data->is_coupon, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)? 1 : 0;
        $promotion->update([
            'start_date' => $data->start_date,
            'end_date' => $data->end_date,
            'status' => $data->status,
            'is_coupon' => $data->is_coupon,
            'coupon_code' => $data->coupon_code?? null,
            'coupon_limit' => $data->coupon_limit?? null,
            'type' => $data->type,
            'location' => $data->location,
            'target' => $data->target,
            'image' => $data->image ?? null,
            'product_id' => $data->product_id ?? null,
            'categories' => $data->categories ?? null,
            'discount' => $data->discount ?? null,
            'max_amount_discount' => $data->max_amount_discount ?? null,
            'brands' => $data->brands ?? [],
            'categories' => $data->categories ?? [],
            'customer_group' => $data->customer_group ?? 'all',
            'bundle_id' => $data->bundle_id ?? null
            
        ]);

        $this->translation->updateTranslations($promotion, $data , ['title' , 'des' , 'meta_title' , 'meta_des']);
        return $promotion;
    }

}