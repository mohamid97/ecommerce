<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Stock;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\StockMovment;

class StoreStockAction
{
    public function addStock($dto)
    {

        $product = Product::findOrFail($dto->product_id);
        // check if front sent variant id and this variant belong to this product

        $this->validateVariantId($product->has_options , $dto->variant_id , $dto->product_id);
        
        return $this->storeStock($dto , $product->has_options);
        
    } 

    // check variant is exists in this product
    private function validateVariantId($has_options , $variant_id , $product_id){

        if($has_options){
          
            if(isset($variant_id) && !$this->checkHasVariantId($variant_id , $product_id) ){
                throw new \Exception('Variant not found');
            }
          
        }
    }

    private function checkHasVariantId($variant_id , $product_id){
        return ProductVariant::where('id' , $variant_id)->where('product_id' , $product_id)->exists();
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



        if($stock->status == 'active'){
            $this->UpdateMainStock($has_options , $dto);
        }

        return $stock;
     }



     private function UpdateMainStock($has_options , $dto){
        if($has_options){
            ProductVariant::where('product_id' , $dto->product_id)->where('id' , $dto->variant_id)
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
        return ProductVariant::where('product_id' , $product_id)->where('id' , $variant_id)->value('stock');
     }


     private function getProductStock($product_id){
        return Product::where('id' , $product_id)->value('stock');
     }







}
