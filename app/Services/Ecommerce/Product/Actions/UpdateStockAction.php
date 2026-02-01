<?php
namespace App\Services\Ecommerce\Product\Actions;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\StockMovment;

class UpdateStockAction
{
    public function updateStock($dto)
    {

      $product = Product::find($dto->product_id);
      $hasVariants = false;
      (isset($product) && $product->has_options) ? $hasVariants = true : $hasVariants = false;
      StockMovment::where('id', $dto->id)->update(
      [
        // 'product_id' => $dto->product_id,
        // 'variant_id' => $hasVariants ? $dto->variant_id : null,
        'quantity' => $dto->quantity,
        'type' => $dto->type ?? 'increase',
        'note' => $dto->note ?? null,
        'cost_price' => $dto->cost_price ?? null,
        'sales_price' => $dto->sales_price,
      ]);

        
    } 





}   