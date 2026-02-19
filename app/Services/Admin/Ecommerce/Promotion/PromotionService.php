<?php
namespace App\Services\Admin\Ecommerce\Promotion;

use App\Models\Api\Ecommerce\Promotion;
use App\Services\Admin\Ecommerce\Promotion\Actions\StorePromotionAction;
use App\Services\Admin\Ecommerce\Promotion\Actions\UpdatePromotionAction;

class PromotionService
{
    public function __construct(
            private readonly StorePromotionAction  $storePromotionAction,
            private readonly UpdatePromotionAction  $updatePromotionAction,

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
    public function deletePromotion($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();
        return true;
    }
}