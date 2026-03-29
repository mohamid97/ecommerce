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

        $banner = null;
    

        if (array_key_exists('banner', $this->data['models'][$key])) {
            
            if($this->data['models'][$key]['banner'] instanceof \Illuminate\Http\UploadedFile){
               $bannerValue =  $this->data['models'][$key]['banner'];
                $banner = $this->uploadImage($bannerValue, 'uploads/banners');
            }else{
                $banner = null;

            }




        }else {
            
            // 4. Not sent at all => delete
            $banner = (isset($metasetting) && isset($metasetting->banner) ) ? $metasetting->banner : null;
        }

        if ($metasetting) {
            $metasetting->update([
                'name'   => $key,
                'banner' => $banner
            ]);
        } else {
            $metasetting = MetaSetting::create([
                'name'   => $key,
                'banner' => $banner
            ]);
        }

        $this->processTranslations(
            $metasetting,
            $this->data['models'][$key],
            ['meta_title', 'meta_des']
        );
    }

    return $this->modelClass::all();
}

   

     
}