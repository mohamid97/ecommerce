<?php

namespace App\Services\Ecommerce\Cart\Strategies;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Services\Ecommerce\Cart\CartAction;
use App\Services\Ecommerce\Cart\CartRepository;
use Illuminate\Validation\ValidationException;

/**
 * Strategy: add a product (that belongs to a bundle) to the cart.
 *
 * Required payload : { bundel_id, product_id, quantity }
 * Optional payload : { bundel_id, product_id, variant_id, quantity }
 */
class BundleStrategy implements CartStrategyInterface
{
    public function __construct(
        protected CartAction     $action,
        protected CartRepository $repo
    ) {}

    public function validate(AddToCartDTO $dto): void
    {
        // 1. Bundle must exist
        $this->action->checkBundelExists($dto->bundel_id);

        // 2. Product must exist and be active
        $this->action->checkProductExists($dto->product_id);

        // 3. Product must belong to this bundle
        $this->action->checkProductBelongsToBundle($dto->product_id);

        // 4. Require variant if product has options
        if ($this->action->product->has_options && !isset($dto->variant_id)) {
            throw ValidationException::withMessages([
                'variant_id' => __('main.variant_is_required_for_this_product')
            ]);
        }

        // 5. Validation specific to whether variant_id is present
        if (isset($dto->variant_id)) {
            $this->action->checkVariantBelongsToBundle($dto->variant_id);
            $this->action->checkStockWithOption($dto->quantity);
        } else {
            $this->action->checkStock($dto->quantity);
        }
    }

    public function store(int $userId, AddToCartDTO $dto): mixed
    {
        return $this->repo->createOrUpdateCard($userId, $dto);
    }

    
}
