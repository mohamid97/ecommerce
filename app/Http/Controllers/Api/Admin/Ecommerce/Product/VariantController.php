<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Product;

use App\DTO\Ecommerce\Product\StoreVaraintDTO;
use App\DTO\Ecommerce\Product\UpdateVaraintDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\AllVarinatsRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\StoreVariantRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\UpdateVaraintRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\VariantCombinationRequest;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint\FilterProductVaraintResource;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint\ProductVaraintsResource;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint\VarinatDetailsResource;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\DeleteVaraintAction;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\FilterProductaraintsAction;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\MakeDefaultVaraintAction;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\GenerateCombinationsVaraintAction;
// use App\Services\Admin\Ecommerce\Product\Actions\Variant\DeleteVaraintAction;
use Illuminate\Support\Facades\DB;
use App\Traits\ResponseTrait;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\StoreVaraintAction;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\UpdateVaraintAction;
use App\Services\Admin\Ecommerce\Product\UpdateProductService;
use Symfony\Component\HttpFoundation\Request;
use App\Imports\VariantsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\ImportVariantsRequest;

class VariantController extends Controller
{
    use ResponseTrait;
    // view varaint details data
    public function viewVariant(Request $request){
        if(!$request->id){
            return $this->error(__('main.id_is_required') , 400);
        }
        $variant = ProductVariant::with(['variants.optionValue.option' , 'varaintImages' , 'product'])->findOrFail($request->id);
        return $this->success(new VarinatDetailsResource($variant)  , __('main.retreived_successfully' , ['model' => 'Variant']) );

    }

    // get all varaints of product 
    public function varintsProduct(AllVarinatsRequest $request){
        $product = Product::findOrFail($request->product_id);
        $variants = ProductVariant::with(['variants.optionValue.option'])->where('product_id' , $request->product_id)->get();

        return $this->success( [
            'product'=>['id'=>$product->id,'title'=>$product->title,'on_demand'=>$product->on_demand , 
            'sale_price'=>(float)$product->sale_price,'price_after_discount'=>(float)$product->getDiscountPrice(),
            ] , 'variants'=>ProductVaraintsResource::collection($variants)] , __('main.retreived_successfully' , ['model' => 'Variant'])  );
        

    }
    // get all combination of varinats 
   public function variantsCombinations(VariantCombinationRequest $request, GenerateCombinationsVaraintAction $generateCombinationsVaraintAction){
       try {
           $result = $generateCombinationsVaraintAction->generate($request->product_id);
           return $this->success($result, __('main.list_successfully', ['model' => 'Combinations']));
       } catch (\Exception $e) {
           return $this->error($e->getMessage(), 500);
       }
   }




    // store new variant 
    public function storeVariant(StoreVariantRequest $request , StoreVaraintAction $storeVariantAction){
        
        try{
                DB::beginTransaction();  
                $DTO = StoreVaraintDTO::fromRequest($request->validated());
                $details = $storeVariantAction->storeVariant($DTO);
                DB::commit();
                return $this->success(new VarinatDetailsResource($details) , __('main.stored_successfully' , ['model' => 'Variant']));

        }catch(\Exception $e){
                DB::rollBack();
                return $this->error($e->getMessage(), 500);
        }

    } // end store variant

    public function updateVariant(UpdateVaraintRequest $request , UpdateVaraintAction $updateVaraintAction){
        try{

                DB::beginTransaction();  
                    $DTO = UpdateVaraintDTO::fromRequest($request->validated());
                    $details = $updateVaraintAction->updateVariant($DTO);
                DB::commit();
                return $this->success(new VarinatDetailsResource($details) , __('main.updated_successfully' , ['model' => 'Variant']));

        }catch(\Exception $e){
                DB::rollBack();
                return $this->error($e->getMessage(), 500); 

        }

    } // end update variant

    public function deleteVariant(Request $request , DeleteVaraintAction $deleteVaraintAction){
        
        try{
            $id = $request->id;
            $details = $deleteVaraintAction->deleteVariant($id);
            return $this->success(new VarinatDetailsResource($details) , __('main.deleted_successfully' , ['model' => 'Variant']));

        }catch(\Exception $e){
            return $this->error($e->getMessage(), 500); 

        }
    } // end delete variant



    public function filterProductaraints(Request $request, FilterProductaraintsAction $filterProductaraintsAction){
        try {
            $filter = $filterProductaraintsAction->filter($request);
            return $this->success(FilterProductVaraintResource::collection($filter), __('main.filtered_successfully', ['model' => 'Variant']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }


    public function makeDefault(Request $request, MakeDefaultVaraintAction $makeDefaultVaraintAction)
    {
        if ($request->variant_id) {
            try {
                $makeDefaultVaraintAction->makeDefault($request->variant_id);
                return $this->success(__('main.updated_successfully'));
            } catch (\Exception $e) {
                return $this->error($e->getMessage(), 500);
            }
        }

        return $this->error(__('main.no_id'));
    }

    // import variants from XLSX
    public function importVariants(ImportVariantsRequest $request)
    {
        try {
            $file = $request->file('file');

            // Note: requires maatwebsite/excel package installed
            $defaultProductId = $request->input('default_product_id');
            Excel::import(new VariantsImport($defaultProductId), $file);
            return $this->success([], __('main.import_started'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }



    







}
