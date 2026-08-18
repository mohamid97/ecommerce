<?php
namespace App\Services\Admin\Ecommerce\Promotion;

use App\Models\Api\Ecommerce\Promotion;
use App\Services\Admin\Ecommerce\Promotion\Actions\StorePromotionAction;
use App\Services\Admin\Ecommerce\Promotion\Actions\UpdatePromotionAction;
use App\Services\Ecommerce\Cart\CartPriceRefreshService;

class PromotionService
{
    public function __construct(
            private readonly StorePromotionAction  $storePromotionAction,
            private readonly UpdatePromotionAction  $updatePromotionAction,
            private readonly CartPriceRefreshService $cartPriceRefresh,

    ) {}

    // Store Promotion
    public function storePromotion($data)
    {
        return $this->storePromotionAction->execute($data);
    }

    public function updatePromotion($data)
    {
       return $this->updatePromotionAction->execute($data);
    }
    public function deletePromotion($request)
    {
   
        if(!$request['id']) {
            throw new \Exception(__('main.id_is_required'));
        }
        $id = $request['id'];
        $promotion = Promotion::findOrFail($id);
        $wasAutomaticPromotion = ! $promotion->is_coupon;
        $promotion->delete();

        if ($wasAutomaticPromotion) {
            $this->cartPriceRefresh->refreshAll();
        }

        return true;
    }
}
