<?php

namespace App\Services\Ecommerce\Productoption;

use App\Models\Api\Admin\Lang;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductOptions;
use App\Models\Api\Ecommerce\ProductOptionValues;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;


class ProductoptionService extends BaseModelService
{

    use StoreMultiLang;
    protected string $modelClass = ProductOptions::class;
    protected $ProductOptionValues;

    public function all($request){
      if(!isset($request['product_id'])){
        throw new \Exception("Product ID is required.");
      }
       return Product::with('options.values.option')->find($request['product_id']);
    }

    public function store()
    {
        $this->checkHasOptions($this->data['product_id']);
        $productOption = parent::store($this->getBasicColumn(['product_id','sku','stock','price']));
        foreach ($this->data['options'] as $option) {
            // store option values and translations
            $this->storeOptionValues($productOption->id, $option['option_name_id']);
            $this->processTranslationsOptions($option);
            
        }

        // return product with options
        return Product::with('options.values.option')->find($productOption->product_id);



    } // end store function

    public function update($id){

        $productOption = ProductOptions::find($id);

        if(!$productOption){
            throw new \Exception("Product Option not found.");
        }
        $this->checkHasOptions($productOption->product_id);

        $productOption = parent::update($id , $this->getBasicColumn(['sku','stock','price']));
        if(isset($this->data['options'])){
            foreach ($this->data['options'] as $option) {
                // check if option value exists
                $optionValue = ProductOptionValues::where('product_option_id', $productOption->id)
                    ->where('option_name_id', $option['option_name_id'])
                    ->first();
                if ($optionValue) {
                    // update translations
                    $this->ProductOptionValues = $optionValue;
                    $this->processTranslationsOptions($option);
                } else {
                    // store new option value and translations
                    $this->storeOptionValues($productOption->id, $option['option_name_id']);
                    $this->processTranslationsOptions($option);
                }
            }
        }

        return Product::with('options.values.option')->find($productOption->product_id);



        
    } // end update function


    public function delete($id){
        $productOption = parent::delete($id);
        return $productOption;
    }








    private function storeOptionValues($productOptionId, $optionId):void{
        $this->ProductOptionValues = ProductOptionValues::create([
            'product_option_id' => $productOptionId,
            'option_name_id' => $optionId,
        ]);
        
    }

    private function processTranslationsOptions($option):void
    {
        foreach (Lang::all() as $lang) {
            if (isset($option['value'][$lang->code])) {
                $this->ProductOptionValues->{'value:'.$lang->code} = $option['value'][$lang->code];
            } else {
                $this->ProductOptionValues->{'value:'.$lang->code} = null;
            }
        }
        $this->ProductOptionValues->save();
    }

    protected function checkHasOptions($productId):void
    {
        if(Product::find($productId)['has_options'] != true){
            throw new \Exception("Product does not have options enabled.");
        }
    }



    
}