<?php

namespace App\Services\Admin\Setting;

use App\Models\Api\Admin\Setting;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;

class SettingService extends BaseModelService{

    
    use StoreMultiLang;
    protected string $modelClass = Setting::class;


    public function all($request){
  
        $allDetails = $this->modelClass::first();
        return $allDetails;
    }


    public function store()
    {   
        $this->uploadSingleImage(['favicon' , 'icon'] , 'uploads/setting');
        $setting = $this->modelClass::updateOrCreate(['id' => 1] , $this->data);
        $this->processTranslations($setting, $this->data, ['title', 'breif' , 'meta_title' , 'meta_des']);  
        return $setting;
             
    }

    

}