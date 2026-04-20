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

    public function validate(AddToCartDTO $dto): void
    {
        $this->action->checkProductExists($dto->product_id);
        $this->action->checkProductHasOption();
        $this->action->checkVariantExists($dto->variant_id);
        $this->action->checkStockWithOption($dto->quantity);
    }

    public function store(int $userId, AddToCartDTO $dto): mixed
    {
        return $this->repo->createOrUpdateCard($userId, $dto);
    }
}
