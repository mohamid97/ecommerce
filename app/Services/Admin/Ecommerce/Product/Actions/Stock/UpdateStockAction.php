<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Stock;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\StockMovment;

class UpdateStockAction
{
    public function updateStock($dto)
    {
      $stock = StockMovment::where('id', $dto->id)->first();
      $oldQty = $stock->quantity;
      $stock->update(
      [
        'quantity' => $dto->quantity,
        'note' => $dto->note ?? null,
        'cost_price' => $dto->cost_price ?? null,
        'sale_price' => $dto->sale_price,
      ]);

      // need to get also old value to remove it from main stock and add new value
      // update main stock ( varaint stock or product Stock)
      $this->updateMainStock($oldQty , $dto->quantity , $stock->product_id , $stock->variant_id);
      return $stock;
        
    } 

    // update status 

    public function updateStatus($data){
      $stock = StockMovment::where('id', $data->batch_id)->update(
      [
        'status' => $data->status,
      ]);



        
    }// end update status


    private function updateMainStock($oldQty , $newQty , $productID , $variantId){
      if($variantId){
        $this->updateVariantStock($oldQty , $newQty , $productID , $variantId);
      }else{
        $this->updateProductStock($oldQty , $newQty , $productID);
      }
        
    }


    private function updateVariantStock($oldQty , $newQty , $productID , $variantId){
      $variant = ProductVariant::where('product_id' , $productID)->where('variant_id' , $variantId)->first();
      $variant->update([
        'stock' => ( $variant->stock - $oldQty ) + $newQty,
      ]);
    }


    private function updateProductStock($oldQty , $newQty , $productID){
      $product = Product::where('id' , $productID)->first();
      $product->update([
        'stock' => ( $product->stock - $oldQty ) + $newQty,
      ]);


      
    }





}   