<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Product;

use App\DTO\Ecommerce\Product\StoreVaraintDTO;
use App\DTO\Ecommerce\Product\UpdateVaraintDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\AllVarinatsRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\StoreVariantRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\UpdateVaraintRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\VariantCombinationRequest;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint\ProductVaraintsResource;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint\VarinatDetailsResource;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\DeleteVaraintAction;
// use App\Services\Admin\Ecommerce\Product\Actions\Variant\DeleteVaraintAction;
use Illuminate\Support\Facades\DB;
use App\Traits\ResponseTrait;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\StoreVaraintAction;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\UpdateVaraintAction;
use App\Services\Admin\Ecommerce\Product\UpdateProductService;

class VariantController extends Controller
{
    use ResponseTrait;


    // get all varaints of product 
    public function varintsProduct(AllVarinatsRequest $request){
        $variants = ProductVariant::with('variants.optionValue.option')->where('product_id' , $request->product_id)->get();

        return $this->success( ProductVaraintsResource::collection($variants) , 'main.retreived_successfully' , ['model' => 'Variant']  );
        

    }
    // get all combination of varinats 
   public function variantsCombinations(VariantCombinationRequest $request){
    $product = Product::findOrFail($request->product_id);
    
    if($product->has_options == 0){
        throw new \Exception('Product has no variants');
    }
    
    $product->load(['options.option', 'options.values.optionValue']);
    
    if ($product->options->isEmpty()) {
        throw new \Exception('Product has no variants');
    }

    $optionsArray = [];
    
    foreach ($product->options as $productOption) {
        $optionName = $productOption->option->title; // e.g., "اللون"
        
        $values = $productOption->values->map(function ($productOptionValue) {
            return [
                'id' => $productOptionValue->optionValue->id,
                'value' => $productOptionValue->optionValue->title
            ];
        })->toArray();

        if (!empty($values)) {
            $optionsArray[$optionName] = $values;
        }
    }

    // Generate combinations AFTER collecting all options
    $combinations = $this->generateCombinations($optionsArray);
    
    return response()->json([
        'success' => true,
        'product_id' => $product->id,
        'total_combinations' => count($combinations),
        'combinations' => $combinations
    ]);
}

private function generateCombinations(array $optionsArray)
{
    if (empty($optionsArray)) {
        return [];
    }

    $result = [[]];
    
    foreach ($optionsArray as $optionName => $values) {
        $temp = [];
        foreach ($result as $combination) {
            foreach ($values as $value) {
                $newCombination = $combination;
                $newCombination[$optionName] = $value;
                $temp[] = $newCombination;
            }
        }
        $result = $temp;
    }

    // Format combinations for better readability
    return array_map(function ($combination) {
        $formatted = [
            'option_value_ids' => [],
            'display_name' => [],
            'attributes' => []
        ];

        foreach ($combination as $optionName => $value) {
            $formatted['option_value_ids'][] = $value['id'];
            $formatted['display_name'][] = "{$optionName}: {$value['value']}";
            $formatted['attributes'][$optionName] = $value['value'];
        }

        $formatted['display_name'] = implode(' | ', $formatted['display_name']);
        
        return $formatted;
    }, $result);
}




 // store new variant 
 public function storeVariant(StoreVariantRequest $request){
     
   try{
        DB::beginTransaction();  
        $DTO = StoreVaraintDTO::fromRequest($request->validated());
        $service = app(StoreVaraintAction::class);
        $details = $service->storeVariant($DTO);
        DB::commit();
        return $this->success(new VarinatDetailsResource($details) , __('main.stored_successfully' , ['model' => 'Variant']));

   }catch(\Exception $e){
        DB::rollBack();
        return $this->error($e->getMessage(), 500); 

   }






 } // end store variant




 public function updateVariant(UpdateVaraintRequest $request){
    try{

        DB::beginTransaction();  
            $DTO = UpdateVaraintDTO::fromRequest($request->validated());
            $service = app(UpdateVaraintAction::class);
            $details = $service->updateVariant($DTO);
        DB::commit();
        return $this->success(new VarinatDetailsResource($details) , __('main.updated_successfully' , ['model' => 'Variant']));

   }catch(\Exception $e){
        DB::rollBack();
        return $this->error($e->getMessage(), 500); 

   }

 } // end update variant

 public function deleteVariant($id){
    try{
            $service = app(DeleteVaraintAction::class);
            $service->deleteVariant($id);
            return $this->success(null , __('main.deleted_successfully' , ['model' => 'Variant']));

   }catch(\Exception $e){
        return $this->error($e->getMessage(), 500); 

   }
 } // end delete variant




}
