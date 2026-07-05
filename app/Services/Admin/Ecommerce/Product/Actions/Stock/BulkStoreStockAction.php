<?php

namespace App\Services\Admin\Ecommerce\Product\Actions\Stock;

use App\DTO\Ecommerce\Product\BulkAddStockDTO;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\StockMovment;

class BulkStoreStockAction
{
    /**
     * @return array<StockMovment>
     */
    public function addStocks(BulkAddStockDTO $dto): array
    {
        $product = Product::findOrFail($dto->product_id);

        if ($product->has_options) {
            $variantIds = $dto->variant_ids ?? ProductVariant::where('product_id', $product->id)->pluck('id')->toArray();

            $this->validateVariantIds($variantIds, $product->id);

            $stocks = [];
            foreach ($variantIds as $variantId) {
                $stocks[] = $this->storeStock($dto, $variantId);
            }

            return $stocks;
        }

        return [$this->storeStock($dto, null)];
    }

    private function validateVariantIds(array $variantIds, int $productId): void
    {
        $variantIds = array_values(array_unique(array_filter($variantIds, fn ($id) => $id !== null)));

        if (empty($variantIds)) {
            throw new \Exception('Variant ids are required for this product');
        }

        $count = ProductVariant::where('product_id', $productId)
            ->whereIn('id', $variantIds)
            ->count();

        if ($count !== count($variantIds)) {
            throw new \Exception('One or more variant ids are invalid for this product');
        }
    }

    private function storeStock(BulkAddStockDTO $dto, ?int $variantId): StockMovment
    {
        $stock = StockMovment::create([
            'product_id' => $dto->product_id,
            'variant_id' => $variantId,
            'quantity' => $dto->quantity,
            'note' => $dto->note,
            'cost_price' => $dto->cost_price,
            'sale_price' => $dto->sale_price,
            'status' => $dto->status ?? 'active',
        ]);

        if ($stock->status === 'active') {
            $this->updateMainStock($stock);
        }

        return $stock;
    }

    private function updateMainStock(StockMovment $stock): void
    {
        if ($stock->variant_id) {
            ProductVariant::where('id', $stock->variant_id)
                ->increment('stock', $stock->quantity);

            return;
        }

        Product::where('id', $stock->product_id)
            ->increment('stock', $stock->quantity);
    }
}
