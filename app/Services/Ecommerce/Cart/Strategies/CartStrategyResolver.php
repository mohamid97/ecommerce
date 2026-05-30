<?php

namespace App\Services\Ecommerce\Cart\Strategies;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Services\Ecommerce\Cart\CartAction;
use App\Services\Ecommerce\Cart\CartRepository;

class CartStrategyResolver
{
    public function __construct(
        protected CartAction     $action,
        protected CartRepository $repo
    ) {}

    /**
     * Pre-process the DTO (auto-fill missing variant IDs) then return the
     * correct strategy.  Strategy resolution order:
     *
     *   bundel_id present           → BundleStrategy
     *   product_id + variant_id     → ProductWithOptionStrategy
     *   product_id only             → SimpleProductStrategy
     */
    public function resolve(AddToCartDTO $dto): CartStrategyInterface
    {
        if (isset($dto->bundel_id)) {
            $this->action->resolveBundleItemVariants($dto);
            return new BundleStrategy($this->action, $this->repo);
        }

        if (isset($dto->product_id) && !isset($dto->variant_id)) {
            $this->action->resolveDefaultVariant($dto);
        }

        if (isset($dto->variant_id)) {
            return new ProductWithOptionStrategy($this->action, $this->repo);
        }

        return new SimpleProductStrategy($this->action, $this->repo);
    }
}
