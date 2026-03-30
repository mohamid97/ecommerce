<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Bundel;

use App\DTO\Ecommerce\Bundel\StoreBundelDTO;
use App\DTO\Ecommerce\Bundel\UpdateBundelDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Ecommerce\Bundel\StoreBundelRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Bundel\UpdateBundelRequest;
use App\Http\Resources\Api\Admin\Ecommerce\Bundel\BundeDetailsResource;
use App\Http\Resources\Api\Admin\Ecommerce\Bundel\BundelResource;
use App\Models\Api\Ecommerce\Bundel;
use App\Services\Admin\Ecommerce\Bundel\BundelService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BundelController extends Controller
{
    use ResponseTrait;
    public function __construct(private BundelService $service) {}


    public function getBundels(Request $request){
        try{
            $bundels = $this->service->getBundels($request->all());
            return $this->success(BundelResource::collection($bundels) , __('main.success'));

        }catch(\Exception $e){
            return $this->error($e->getMessage() , 500);
        }

    }
    public function storeBundel(StoreBundelRequest $request){
        try{
            DB::beginTransaction();
            $dto = StoreBundelDTO::fromRequest($request->all());
            $details = $this->service->storeBundel($dto);
            DB::commit();
            return $this->success(new BundeDetailsResource($details) , __('main.stored_successfully' , ['model'=>'Bundel']));

        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage() , 500);
        }

    }

    public function updateBundel(UpdateBundelRequest $request){
        try{
            DB::beginTransaction();
            $dto = UpdateBundelDTO::fromRequest($request->all());
            $details = $this->service->updateBundel($dto);
            DB::commit();
            return $this->success(new BundeDetailsResource($details) , __('main.updated_successfully' , ['model'=>'Bundel']));

        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage() , 500);
        }

    } // end update bundel 



    public function deleteBundel(Request $request){
        try{
            DB::beginTransaction();
            $details = $this->service->deleteBundel($request->all());
            DB::commit();
            return $this->success(new BundeDetailsResource($details) , __('main.deleted_successfully' , ['model'=>'Bundel']));

        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage() , 500);
        }
    } // delete bundel 


    public function bundelDetails(Request $request){
      
        try{
              
            $details = Bundel::with(['bundelDetails' , 'category' , 'brand'])->findOrFail($request->bundel_id);
            return $this->success(new BundeDetailsResource($details) , __('main.success') );

        }catch(\Exception $e){
            return $this->error($e->getMessage() , 500);
        }

        
    }
}
