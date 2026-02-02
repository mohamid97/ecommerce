<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Stock;
use App\Models\Api\Ecommerce\StockMovment;

class DetailsStockAction
{
    public function show($request)
    {
        if(isset($request->variant_id))
            $batches = StockMovment::where('product_id', $request->product_id)->where('variant_id', $request->variant_id)->get();
        else
            $batches = StockMovment::where('product_id', $request->product_id)->get();

        return $batches;
    } // all batches


    public function batchDetails($batch_id)
    {
        return StockMovment::findOrFail($batch_id);
    } // batch details

    


}