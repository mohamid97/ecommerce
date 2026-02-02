<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\DTO\Ecommerce\Product\AddStockDTO;
use App\DTO\Ecommerce\Product\UpdateStockDTO;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Stock\AddStockRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Stock\StockDetailsRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Stock\UpdateStatusRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Stock\UpdateStockRequest;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Stock\StockDetailsResource;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Stock\StocksResource;
use App\Services\Admin\Ecommerce\Product\Actions\Stock\DeleteStockAction;
use App\Services\Admin\Ecommerce\Product\Actions\Stock\DetailsStockAction;
use App\Services\Admin\Ecommerce\Product\Actions\Stock\StoreStockAction;
use App\Services\Admin\Ecommerce\Product\Actions\Stock\UpdateStockAction;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    use ResponseTrait;

    // get batch detils 
    public function batchDetails(Request $request){
        if(!$request->batch_id){
            return $this->error(__('main.no_id'), 422);
        }
          $service = app(DetailsStockAction::class);
          $batch = $service->batchDetails($request->batch_id);
          return $this->success(new StockDetailsResource($batch) , __('main.retrieved_successfully' , ['model' => 'Stock']));
    }
    // get batches or all stocks 
    public function getBatches(StockDetailsRequest $request){
        try{

            $service = app(DetailsStockAction::class);
            $batches = $service->show($request);       
            return $this->success(StocksResource::collection($batches) , __('main.retrieved_successfully' , ['model' => 'Stock']));
        }catch(\Exception $e){
            return $this->error($e->getMessage(), 500);
        }
    }
    public function addStock(AddStockRequest $request){
        try{
            DB::beginTransaction();
            $dto = AddStockDTO::fromRequest($request->validated());
            $service = app(StoreStockAction::class);
            $data = $service->addStock($dto);
            DB::commit();
            return $this->success(new StockDetailsResource($data) , __('main.stored_successfully' , ['model' => 'Stock']));
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



    //delete batch

    public function deleteBatch(Request $request){
        if(!$request->batch_id){
            return $this->error(__('main.no_id'), 422);
        }
        try{
            DB::beginTransaction();
            $service = app(DeleteStockAction::class);
            $service->deleteBatch($request->batch_id);
            DB::commit();
            return $this->success(__('main.deleted_successfully' , ['model' => 'Stock']));
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    } // end delete batch


    // update status stock or batch
    public function updateStatus(UpdateStatusRequest $request){
        try{
            DB::beginTransaction();
            $service = app(UpdateStockAction::class);
            $service->updateStatus($request->validated());
            DB::commit();
            return $this->success(__('main.updated_successfully' , ['model' => 'Stock']));
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    } // end update status 


    

}
