<?php

namespace App\Services\Ecommerce\Cart\Strategies;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Services\Ecommerce\Cart\CartAction;
use App\Services\Ecommerce\Cart\CartRepository;

/**
 * Resolves the correct CartStrategy based on the DTO payload:
 *
 *   bundel_id presence         →  BundleStrategy
 *   variant_id presence        →  ProductWithOptionStrategy
 *   Neither                    →  SimpleProductStrategy
 */
class CartStrategyResolver
{
    public function __construct(
        protected CartAction     $action,
        protected CartRepository $repo
    ) {}

    public function resolve(AddToCartDTO $dto): CartStrategyInterface
    {
        // ── Bundle ──────────────────────────────────────────────────────────
        // If bundel_id is sent, it's a bundle item.
        if (isset($dto->bundel_id)) {
            return new BundleStrategy($this->action, $this->repo);
        }

        // ── Product + Variant (with option) ─────────────────────────────────
        if (isset($dto->variant_id)) {
            return new ProductWithOptionStrategy($this->action, $this->repo);
        }

        // ── Plain product (no option) ────────────────────────────────────────
        return new SimpleProductStrategy($this->action, $this->repo);
    }
}
