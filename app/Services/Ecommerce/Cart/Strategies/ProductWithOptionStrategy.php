<?php

namespace App\Services\Ecommerce\Cart\Strategies;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Services\Ecommerce\Cart\CartAction;
use App\Services\Ecommerce\Cart\CartRepository;

/**
 * Strategy: add a product together with one of its option variants.
 * Payload: { product_id, variant_id, quantity }
 */
class ProductWithOptionStrategy implements CartStrategyInterface
{
    public function __construct(
        protected CartAction     $action,
        protected CartRepository $repo
    ) {}

    public function validate(int $userId, AddToCartDTO $dto): void
    {
        //$this->action->validateMOQ('variant',$dto->variant_id, $dto->quantity);
        // check if product exists 
        $this->action->checkProductExists($dto->product_id);
        // check if product has option
        $this->action->checkProductHasOption();
        // then check if variant exists and stock is available
        $this->action->checkVariantExists($dto->variant_id);
        $this->action->ensureUnitsAreConfigured();

        // Final demand = existing demand for this variant across whole cart + new quantity
        $existing = $this->action->getTotalCartDemand($userId, $dto->product_id, $dto->variant_id);
        $this->action->checkStockWithOption($existing + $dto->quantity);
    }

    public function store(int $userId, AddToCartDTO $dto): mixed
    {
        return $this->repo->createOrUpdateCard($userId, $dto);
    }
}
