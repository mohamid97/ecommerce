<?php
namespace App\Services\Admin\Mediaimage;

use App\Models\Api\Admin\Mediaimage;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;

class MediaimageService extends BaseModelService
{   
    use StoreMultiLang;
   protected string $modelClass = Mediaimage::class;
    
    public function store()
    {
        $this->uploadSingleImage(['image'], 'uploads/mediaimages'); 
        $media = parent::store($this->getBasicColumn(['image']));
        $this->processTranslations($media, $this->data, ['title', 'des']);  
        return $media;
        
    }

    public function update($id){
        $this->uploadSingleImage(['image'], 'uploads/mediaimages'); 
        $media = parent::update($id , $this->getBasicColumn( ['image']));
        $this->processTranslations($media, $this->data, ['title', 'des']);
        return $media;        
    }



    



    

}