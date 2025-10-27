<?php
namespace App\Services\Admin\Mediavideo;

use App\Models\Api\Admin\Mediavideo;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;


class MediavideoService extends BaseModelService{
    
    use StoreMultiLang;
    protected string $modelClass = Mediavideo::class;
    
    public function store()
    {
        $media = parent::store($this->getBasicColumn(['link']));
        $this->processTranslations($media, $this->data, ['title', 'des']);  
        return $media;    
    }

    public function update($id){
        $media = parent::update($id , $this->getBasicColumn( ['link']));
        $this->processTranslations($media, $this->data, ['title', 'des']);
        return $media;        
    }

    


    
}