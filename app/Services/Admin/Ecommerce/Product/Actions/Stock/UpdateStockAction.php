<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Stock;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\StockMovment;

class UpdateStockAction
{
    public function updateStock($dto)
    {
      StockMovment::where('id', $dto->id)->update(
      [
        'quantity' => $dto->quantity,
        'note' => $dto->note ?? null,
        'cost_price' => $dto->cost_price ?? null,
        'sales_price' => $dto->sales_price,
      ]);

        
    } 

    // update status 

    public function updateStatus($data){
      StockMovment::where('id', $data->batch_id)->update(
      [
        'status' => $data->status,
      ]);
        
    }// end update status





}   