<?php
namespace App\Services\Ecommerce\Product\Actions;
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


    public function batchDetails($request)
    {
        return StockMovment::findOrFail($request->batch_id);
    } // batch details

    


}