<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\DTO\Ecommerce\Product\AddStockDTO;
use App\DTO\Ecommerce\Product\UpdateStockDTO;
use App\Http\Requests\Api\Ecommerce\Product\AddStockRequest;
use App\Http\Requests\Api\Ecommerce\Product\UpdateStockRequest;
use App\Services\Ecommerce\Product\Actions\DetailsStockAction;
use App\Services\Ecommerce\Product\Actions\StoreStockAction;
use App\Services\Ecommerce\Product\Actions\UpdateStockAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    // get batches or all stocks 
    public function getBatches(Request $request){
        try{

            $service = app(DetailsStockAction::class);
            $batches = $service->show($request);       
            return $this->success($batches);
        }catch(\Exception $e){
            return $this->error($e->getMessage(), 500);
        }
    }
    public function addStock(AddStockRequest $request){
        try{
            DB::beginTransaction();
            $dto = AddStockDTO::fromRequest($request->validated());
            $service = app(StoreStockAction::class);
            $service->addStock($dto);
            DB::commit();
            return $this->success('Stock added successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }


    } // end add stock

    // update Stock
    public function updateStock(UpdateStockRequest $request){
        try{
            DB::beginTransaction();
            $dto = UpdateStockDTO::fromRequest($request->validated());
            $service = app(UpdateStockAction::class);
            $service->updateStock($dto);
            DB::commit();
            return $this->success('Stock updated successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }


    } // end update Stock


    

}
