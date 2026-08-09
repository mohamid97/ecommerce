<?php

namespace App\Http\Resources\Api\Front\Ecommerce\Concerns;

use App\Models\Api\Ecommerce\BundelDetails;

trait CalculatesCartMaximumQuantity
{
    /**
     * Return the largest quantity that can be selected without exceeding stock.
     * The result is rounded down to a valid MOQ multiple. For a bundle, the
     * component with the lowest available bundle count determines the limit.
     */
    protected function maximumQuantity(): int
    {
        if ($this->bundel_id && $this->type === 'bundel') {
            return $this->maximumBundleQuantity();
        }

        $inventoryItem = $this->variant ?? $this->product;
        $moq = $this->variant?->moq ?? $this->product?->moq ?? 1;

        return $this->quantityAllowedByStockAndMoq($inventoryItem?->stock, $moq);
    }

    protected function maximumBundleQuantity(): int
    {
        $maximum = null;

        foreach ($this->cartBundelItems as $bundleItem) {
            $detail = $bundleItem->bundleDetail;

            if (!$detail) {
                $detail = BundelDetails::query()
                    ->where('bundel_id', $this->bundel_id)
                    ->when(
                        $bundleItem->bundle_item_id,
                        fn ($query) => $query->whereKey($bundleItem->bundle_item_id),
                        fn ($query) => $query->where('product_id', $bundleItem->product_id)
                    )
                    ->first();
            }

            $unitsPerBundle = max(1, (int) ($detail?->quantity ?? 1));
            $inventoryItem = $bundleItem->variant ?? $bundleItem->product;
            $moq = $bundleItem->variant?->moq ?? $bundleItem->product?->moq ?? 1;
            $availableUnits = $this->quantityAllowedByStockAndMoq($inventoryItem?->stock, $moq);
            $bundleLimit = intdiv($availableUnits, $unitsPerBundle);

            $maximum = $maximum === null ? $bundleLimit : min($maximum, $bundleLimit);
        }

        return $maximum ?? 0;
    }

    protected function quantityAllowedByStockAndMoq(mixed $stock, mixed $moq): int
    {
        $availableStock = max(0, (int) $stock);
        $minimumOrderQuantity = max(1, (int) $moq);

        return intdiv($availableStock, $minimumOrderQuantity) * $minimumOrderQuantity;
    }
}
