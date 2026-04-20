<?php

namespace App\Services\Ecommerce\Cart\Strategies;

use App\DTO\Ecommerce\Cart\AddToCartDTO;

interface CartStrategyInterface
{
    /**
     * Validate the incoming DTO (stock, existence, options …).
     * Throws an exception on failure.
     */
    public function validate(AddToCartDTO $dto): void;

    /**
     * Persist the cart item and return the cart model.
     */
    public function store(int $userId, AddToCartDTO $dto): mixed;
}
