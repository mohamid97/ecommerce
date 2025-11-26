<?php
namespace App\Services\Admin\Des;

use App\Models\Api\Admin\Des;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;

class DesService extends BaseModelService
{

    use StoreMultiLang;
    protected string $modelClass = Des::class;


        public function all($request){
        $allDetails = parent::all($request);
        return $allDetails;
    }

    public function view($id){
        $desDetails = parent::view($id);
        return $desDetails;
    }

    public function store()
    {
        $this->uploadSingleImage(['des_image'  , 'breadcrumb'], 'uploads/des'); 
        $des = parent::store($this->getBasicColumn(['category_image', 'breadcrumb']));
        $this->processTranslations($des, $this->data, ['title',  'des' , 'alt_image' , 'title_image' , 'small_des' , 'meta_title' , 'meta_des']);  

        return $des;
        
    }


    public function update($id){
        $this->uploadSingleImage(['des_image'  , 'breadcrumb'], 'uploads/des'); 
        $des = parent::update($id , $this->getBasicColumn(['category_image', 'breadcrumb']));
        $this->processTranslations($des, $this->data, ['title',  'des' , 'alt_image' , 'title_image' , 'small_des' , 'meta_title' , 'meta_des']);  
        return $des;        
    }
    
    public function delete($id){
        $des = parent::delete($id);
        return $des;
    }



    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }

    

    
}