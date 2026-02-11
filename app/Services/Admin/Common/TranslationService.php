<?php
namespace App\Services\Admin\Common;

use App\Models\Api\Admin\Lang;

class TranslationService{
    public function storeTranslations($model, $data , array $columns){
        foreach(Lang::all() as $locale){
            foreach($columns as $column){
                if(isset($data->$column[$locale->code])){
                    $model->translateOrNew($locale->code)->$column = $data->$column[$locale->code];
                }   
            }
            $model->save();
        }
    } // end store translation 

    public function updateTranslations($model, $data , array $columns){
        foreach(Lang::all() as $locale){
            foreach($columns as $column){
                if(isset($data->$column[$locale->code])){
                    $model->translateOrNew($locale->code)->$column = $data->$column[$locale->code];
                }   
            }
            $model->save();
        }
    } // end update translation


    

}