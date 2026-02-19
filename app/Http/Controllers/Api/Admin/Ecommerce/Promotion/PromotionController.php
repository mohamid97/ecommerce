<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Promotion;

use App\DTO\Ecommerce\Promotion\StorePromotionDTO;
use App\DTO\Ecommerce\Promotion\UpdatePromotionDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Ecommerce\Promotion\StorePromotionRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Promotion\UpdatePromotionRequest;
use App\Http\Resources\Api\Admin\Promotion\PromotionDetailsResource;
use App\Http\Resources\Api\Admin\Promotion\PromotionsResource;
use App\Models\Api\Ecommerce\Promotion;
use App\Services\Admin\Ecommerce\Promotion\PromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{

    public function __construct(
        private readonly PromotionService $promotionService,
    ) {}


    public function index(){
        return $this->success(PromotionsResource::collection(Promotion::all()) , __('main.retrieved_successfully' , ['model'=>'Promotions']));
    }
    // Store Promotion
    public function storePromotion(StorePromotionRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = StorePromotionDTO::fromRequest($request->all());
            $promotion = $this->promotionService->storePromotion($data);
            DB::commit();
            return $this->success(new PromotionDetailsResource($promotion) ,  __('main.stored_successfully' , ['model'=>'Promotion']));
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage() , 500);
        }



    } // end store promotion 

    public function updatePromotion(UpdatePromotionRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = UpdatePromotionDTO::fromRequest($request->all());
            $promotion = $this->promotionService->updatePromotion($data);
            DB::commit();
            return $this->success(new PromotionDetailsResource($promotion) ,  __('main.updated_successfully' , ['model'=>'Promotion']));
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage() , 500);
        }
    } //end update promotion




    public function deletePromotion(Request $request)
    {
        try{
            DB::beginTransaction();
            $details = $this->promotionService->deletePromotion($request->all());
            DB::commit();
            return $this->success(new PromotionDetailsResource($details) ,  __('main.deleted_successfully' , ['model'=>'Promotion']));
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage() , 500);
        }
    } //end delete promotion




}
