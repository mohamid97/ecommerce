<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\StoreVariantRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\VariantCombinationRequest;
use App\Models\Api\Admin\Product;
use Illuminate\Http\Request;

class VariantController extends Controller
{
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

 }



}
