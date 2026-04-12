<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Stock;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
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
        $stock = StockMovment::create([
                'product_id' => $dto->product_id,
                'variant_id' => $has_options ? $dto->variant_id : null,
                'quantity' => $dto->quantity,
                'note' => $dto->note ?? null,
                'cost_price' => $dto->cost_price ?? null,
                'sale_price' => $dto->sale_price,
                'status'     => $dto->status ?? 'active',
            ]);

          



            $this->UpdateMainStock($dto);
            return $stock;
     }



     private function UpdateMainStock($dto){
        if($dto->variant_id){
            ProductVariant::where('product_id' , $dto->product_id)->where('variant_id' , $dto->variant_id)
                            ->update([
                                'stock' => $dto->quantity + $this->getVariantStock($dto->product_id , $dto->variant_id),
                                // 'sale_price' => $dto->sale_price,
                            ]);
        }else{
          
            Product::where('id' , $dto->product_id)->update([
                'stock' => $dto->quantity + $this->getProductStock($dto->product_id),
                // 'sale_price' => $dto->sale_price,
            ]);

        }



     }


     private function getVariantStock($product_id , $variant_id){
        return ProductVariant::where('product_id' , $product_id)->where('variant_id' , $variant_id)->value('stock');
     }


     private function getProductStock($product_id){
        return Product::where('id' , $product_id)->value('stock');
     }







}
