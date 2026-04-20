<?php

namespace App\Services\Ecommerce\Cart;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Services\Ecommerce\Cart\Strategies\CartStrategyResolver;

class CartService
{
    public function __construct(
        protected CartRepository     $repo,
        protected CartAction         $action,
        protected CartStrategyResolver $resolver
    ) {}

    /**
     * Add / update a cart item using the appropriate strategy.
     *
     * Strategies resolved from the DTO:
     *   • bundel_id only            → BundleStrategy
     *   • product_id + variant_id   → ProductWithOptionStrategy
     *   • product_id only           → SimpleProductStrategy
     */
    public function StoreToCart(int $userId, AddToCartDTO $dto): mixed
    {
        $strategy = $this->resolver->resolve($dto);

        $strategy->validate($dto);

        return $strategy->store($userId, $dto);
    }

    /**
     * Remove a cart item. Keeps the existing plain logic – no strategy needed here.
     */
    public function RemoveFromCart(int $userId, $dto): void
    {
        $this->action->checkProductExists($dto->productId);

        if (isset($dto->variantId)) {
            $this->action->checkProductHasOption();
            $this->action->checkVariantExists($dto->variantId);
        }

        $this->repo->removeFromCart($userId, $dto);
    }
}