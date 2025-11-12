<?php

namespace App\Services\Ecommerce\Productoption;

use App\Models\Api\Admin\Lang;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\OptionValue;
use App\Models\Api\Ecommerce\ProductOptions;
use App\Models\Api\Ecommerce\ProductOptionValue;
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
       return Product::with(['options.options.option' , 'options.options.optionValue'])->find($request['product_id']);
    }

    public function store()
    {
        $this->checkHasOptions($this->data['product_id']);
        $productOption = parent::store($this->getBasicColumn(['product_id','sku','stock','price']));
        foreach ($this->data['options'] as $option) {
            // validate that value belong to option store option values 
            $this->storeOptionValues($productOption->id, $option);   
        }
        // return product with options
        return Product::with(['options.options.option' , 'options.options.optionValue'])->find($productOption->product_id);



    } //end store function

    public function update($id){

        $productOption = ProductOptions::find($id);

        if(!$productOption){
            throw new \Exception("Product Option not found.");
        }
        $this->checkHasOptions($productOption->product_id);

        $productOption = parent::update($id , $this->getBasicColumn(['sku','stock','price']));
        if(isset($this->data['options'])){
            $productOption->options()->delete(); // remove old options
            foreach ($this->data['options'] as $option) {
               $this->storeOptionValues($productOption->id , $option);     
            }
        }

        return Product::with(['options.options.option' , 'options.options.optionValue'])->find($productOption->product_id);



        
    } // end update function


    public function delete($id){
        $productOption = parent::delete($id);
        return $productOption;
    }








    private function storeOptionValues($productOptionId, $option):void{
        
        // check if already this value belong to option 
        $exist = OptionValue::where('option_id', $option['option_id'])
            ->where('id', $option['option_value_id'])
            ->exists();
        if (!$exist) {
            throw new \Exception("Option Value does not belong to the specified Option.");
        }
        $this->ProductOptionValues = ProductOptionValue::create([
            'product_option_id' => $productOptionId,
            'option_id' => $option['option_id'],
            'option_value_id' =>$option['option_value_id'],
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