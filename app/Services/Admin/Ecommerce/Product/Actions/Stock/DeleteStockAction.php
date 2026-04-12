<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Stock;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\StockMovment;

class DeleteStockAction{
    public function deleteBatch($batch_id){

        // why this not throw error if not exits
        // this reurn success if not id exits 
        $stockMovement = StockMovment::find($batch_id);

        if (!$stockMovement) {
            throw new \Exception(__('main.model_not_found_id' , ['model' => 'Stock' , 'id' => $batch_id]));
        }
        $stockMovement->delete();

        // need to update mainStock 
        if($stockMovement->quantity > 0){
           $this->updateMainStock($stockMovement->quantity , $stockMovement->product_id , $stockMovement->variant_id);

        }



    }



    private function updateMainStock($quantity , $ProductId , $variantId){
        if($variantId){
            $variant = ProductVariant::findOrFail($variantId);
            $variant->stock -= $quantity;
            $variant->save();
        }else{
            $product = Product::findOrFail($ProductId);
            $product->stock -= $quantity;
            $product->save();
        }

    }


    



}