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

    public function resolve(AddToCartDTO $dto): CartStrategyInterface
    {
   
        if (isset($dto->bundel_id)) {
            return new BundleStrategy($this->action, $this->repo);
        }

        if (isset($dto->variant_id)) {
            return new ProductWithOptionStrategy($this->action, $this->repo);
        }
 
      
        return new SimpleProductStrategy($this->action, $this->repo);
    }
}
