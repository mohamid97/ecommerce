<?php
namespace App\Services\Admin\Branch;

use App\Models\Api\Admin\Branch;
use App\Models\Api\Admin\Event;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;

class BranchService extends BaseModelService{

    use StoreMultiLang;
    protected string $modelClass = Branch::class;

    public function all($request){
        $eventDetails = parent::all($request);
        return $eventDetails;
    }

    public function view($id){
        $eventDetails = parent::view($id);
        return $eventDetails;
    }

    public function store()
    {        
      if(isset($this->data['images']) && is_array($this->data['images'])){   
         $this->data['images'] = $this->uploadImages($this->data , 'uploads/branch');
       }    
        $brnach = parent::store($this->getBasicColumn(['images', 'status', 'numbers' , 'location'])); 
        $this->processTranslations($brnach, $this->data, ['title', 'des']);  
        return $brnach;
            
    }
    


    public function update($id ){

        if(isset($thius->data['images']) && is_array($this->data['images'])){
          $data['images'] = $this->uploadImages($this->data , 'uploads/branch');
        }

        $branch = parent::update($id , $this->getBasicColumn(['images', 'status', 'numbers' , 'location']));
        $this->processTranslations($branch, $this->data, ['title', 'des']);
        return $branch;
        
    }

    public function delete($id){
        $branch = Branch::findOrFail($id);
        foreach($branch->images as $image) {
            $this->deleteImage($image);
        }
        $branch = parent::delete($id);
        return $branch;

        
    }



    
}