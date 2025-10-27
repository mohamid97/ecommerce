<?php

namespace App\Services\Admin\Ourteam;

use App\Models\Api\Admin\Ourteam;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;

class OurteamService extends BaseModelService
{
    use StoreMultiLang;
    protected string $modelClass = Ourteam::class;


    // merge column
    private function mergeColumn(){
        if($this->data['social'] ?? false){
            $this->data = array_merge($this->data , $this->data['social']);
            unset($this->data['social']);
        }
    }

    public function store()
    {
        $this->uploadSingleImage(['image'], 'uploads/ourteam'); 
        $this->mergeColumn();
        $ourteam = parent::store($this->getBasicColumn(['image', 'facebook' , 'twitter' , 'linkedin' , 'instagram' ,'youtube' , 'tiktok']));
        $this->processTranslations($ourteam, $this->data, ['position','name','experience','des']);  
        return $ourteam;
        
    }


    public function update($id){
        $this->uploadSingleImage(['image'], 'uploads/ourteam'); 
        $this->mergeColumn();
        $ourteam = parent::update($id , $this->getBasicColumn( ['image', 'facebook' , 'twitter' , 'linkedin' , 'instagram' ,'youtube' , 'tiktok']));
        $this->processTranslations($ourteam, $this->data, ['position','name','experience','des']);
        return $ourteam;        
    }


    public function view($id){
        $ourteamDetails = parent::view($id);
        return $ourteamDetails;
    }


    public function delete($id){
        $ourteam = parent::delete($id);
        return $ourteam;
    }










    
}