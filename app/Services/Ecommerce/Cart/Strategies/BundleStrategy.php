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
        protected CartAction $action,
        protected CartRepository $repo
    ) {}

    public function validate(AddToCartDTO $dto): void
    {
        // 1. Bundle must exist
        $this->action->checkBundelExists($dto->bundel_id);

        if (empty($dto->bundle_items)) {
            throw ValidationException::withMessages([
                'bundle_items' => __('main.bundle_items_are_required'),
            ]);
        }
        $requiredDetailCount = $this->action->bundel->bundelDetails->count();

        if (count($dto->bundle_items) !== $requiredDetailCount) {
            throw ValidationException::withMessages([
                'bundle_items' => __('main.missing_products_in_bundle_items'),
            ]);
        }

        $matchedDetailIds = [];

        // Validate each item and check stock using bundle detail quantity * requested bundle quantity
        foreach ($dto->bundle_items as $item) {
            $productId = $item['product_id'];
            $variantId = $item['variant_id'] ?? null;

            // 2. Product must exist and be active
            $this->action->checkProductExists($productId);

            // 3. Product must belong to this bundle (sets bundelDetail on action)
            $this->action->checkProductBelongsToBundle($productId, $variantId, $matchedDetailIds);

            $detailId = $this->action->bundelDetail?->getKey();
            if ($detailId !== null) {
                $matchedDetailIds[] = $detailId;
            }

            // get per-bundle required qty from bundle detail
            $perBundleQty = $this->action->bundelDetail->quantity ?? 1;
            $totalRequestedQty = $dto->quantity * $perBundleQty;

            // 4. Require variant if product has options
            if ($this->action->product->has_options && ! $variantId) {
                throw ValidationException::withMessages([
                    'variant_id' => __('main.variant_is_required_for_this_product')." (Product ID: $productId)",
                ]);
            }

            // 5. Validation specific to whether variant_id is present
            if ($variantId) {
                $this->action->checkVariantBelongsToBundle($variantId);
                $this->action->checkStockWithOption($totalRequestedQty);
            } else {
                $this->action->checkStock($totalRequestedQty);
            }
        }

        if (count($matchedDetailIds) !== $requiredDetailCount) {
            throw ValidationException::withMessages([
                'bundle_items' => __('main.missing_products_in_bundle_items'),
            ]);
        }
    }

    public function store(int $userId, AddToCartDTO $dto): mixed
    {
        $priceData = $this->action->getBundlePriceWithData($dto);

        return $this->repo->createOrUpdateCard($userId, $dto, $priceData);
    }
}
