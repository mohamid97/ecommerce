<?php

namespace App\Services\Ecommerce\Cart\Strategies;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Services\Ecommerce\Cart\CartAction;
use App\Services\Ecommerce\Cart\CartRepository;
use Illuminate\Validation\ValidationException;

/**
 * Strategy: add a plain product (no variant / option).
 * Payload: { product_id, quantity }
 */
class SimpleProductStrategy implements CartStrategyInterface
{
    public function __construct(
        protected CartAction     $action,
        protected CartRepository $repo
    ) {}

    public function validate(AddToCartDTO $dto): void
    {
        $this->action->checkProductExists($dto->product_id);
        
        // If the product actually has variations, they MUST provide a variant_id.
        if ($this->action->product->has_options) {
            throw ValidationException::withMessages([
                'variant_id' => __('main.variant_is_required_for_this_product')
            ]);
        }
       
        $this->action->checkStock($dto->quantity);
    }

    public function store(int $userId, AddToCartDTO $dto): mixed
    {
    
        return $this->repo->createOrUpdateCard($userId, $dto);
    }
}
