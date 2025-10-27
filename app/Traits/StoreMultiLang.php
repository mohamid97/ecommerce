<?php
namespace App\Traits;

use App\Models\Api\Admin\Lang;

trait StoreMultiLang{

    public function __construct()
    {
        $this->langs = Lang::all();
    }         

     public function processTranslations($model, $data, $attributes)
    {
        
        foreach ($this->langs as $lang) {
            foreach ($attributes as $attribute) {
               
                if (isset($data[$attribute][$lang->code])) {
                    $model->{$attribute.':'.$lang->code} = $data[$attribute][$lang->code];
                }else{               
                    $model->{$attribute.':'.$lang->code} = null;
                }
            }
        }
        $model->save();
  
      
        return $model;
    }




    private function createSlug($data){
        $slug = [];
        foreach($data['title'] as $key => $value){          
            if(!empty($value) && !isset($data['slug'][$key])){
                $slug[$key] = str_replace(' ', '-', strtolower($value));
            }else{
                $slug[$key] = $data['slug'][$key];
            }
        }

        return $slug;
    }





    



    

    
}