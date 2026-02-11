<?php

namespace App\Services\Admin\Ecommerce\Bundel;

use App\Models\Api\Ecommerce\Bundel;
use App\Services\Admin\Ecommerce\Bundel\Actions\StoreBundelAction;
use App\Services\Admin\Ecommerce\Bundel\Actions\UpdateBundelAction;
use Exception;

class BundelService{
    public function storeBundel($data , StoreBundelAction $store){
        return $store->storeBundel($data);

        
    }


    public function updateBundel($data , UpdateBundelAction $update){
        return $update->updateBundel($data);
    }


    public function deleteBundel($data){
        if(Bundel::where('id' , $data->bundel_id)->exists()){
            Bundel::where('id' , $data->bundel_id)->delete();
            return true;
        }
        throw new Exception(__('main.not_found' , ['model'=>'Bundel']));
    } // end delete bundel


    





}