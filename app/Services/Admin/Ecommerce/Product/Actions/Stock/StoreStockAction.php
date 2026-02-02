<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Stock;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\StockMovment;

class StoreStockAction
{
    public function addStock($dto)
    {

        // get product
        $product = Product::findOrFail($dto->product_id);
        // check if front sent variant id and this variant belong to this product
        if(isset($dto->variant_id) && !$product->checkHasVariantId($dto->variant_id)){
            throw new \Exception('Variant not found');
        }

        return $this->storeStock($dto , $product->has_options);
        
    } 



     private function storeStock($dto , $has_options){
        return StockMovment::create([
                'product_id' => $dto->product_id,
                'variant_id' => $has_options ? $dto->variant_id : null,
                'quantity' => $dto->quantity,
                'note' => $dto->note ?? null,
                'cost_price' => $dto->cost_price ?? null,
                'sale_price' => $dto->sale_price,
            ]);
     }







}
