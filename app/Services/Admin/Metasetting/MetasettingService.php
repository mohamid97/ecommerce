<?php

namespace App\Services\Admin\Metasetting;

use App\Models\Api\Admin\Metasetting;
use App\Services\BaseModelService;
use App\Traits\HandlesImage;
use App\Traits\StoreMultiLang;


class MetasettingService extends BaseModelService
{
    use StoreMultiLang , HandlesImage;
    
   protected string $modelClass = Metasetting::class;


   public function store()
   {
   

        foreach ($this->data['models'] as $key => $value) {
   
            $metasetting = MetaSetting::where('name', $key)->first();

            $banner = $this->uploadImage($this->data['models'][$key]['banner'] , 'uploads/banners'); 
                       
            if (isset($metasetting)) {
                $metasetting->update(['name' => $key , 'banner'=>($banner) ? $banner :  $metasetting->banner]);
            } else {                       
                $metasetting = MetaSetting::create([
                    'name'   => $key,
                    'banner' => $banner
                ]);             
               
            }
            
            
            $this->processTranslations($metasetting, $this->data['models'][$key], ['meta_title',  'meta_des']);
        }

        return $this->modelClass::all();

       
   }

   

     
}