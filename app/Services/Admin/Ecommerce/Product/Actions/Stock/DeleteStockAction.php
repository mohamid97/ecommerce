<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Stock;

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


    }
}