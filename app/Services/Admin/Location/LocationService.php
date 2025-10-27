<?php
namespace App\Services\Admin\Location;

use App\Models\Api\Admin\Location;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;
use Illuminate\Support\Str;


class LocationService extends BaseModelService
{
    use StoreMultiLang;
    protected string $modelClass = Location::class;



    public function store()
    {     
        $location = parent::store($this->getBasicColumn(['location']));
        $this->processTranslations($location, $this->data, ['address' , 'government','country']);   
        $location =  $this->storeRelations($this->data, ['phones' , 'emails'], $location);
        return $location;
        
    }

    


    public function update($id){    
        
        $location = parent::update($id , $this->getBasicColumn('location'));
        $this->processTranslations($location, $this->data, ['address' , 'government','country']);
        $location =  $this->storeRelations($this->data, ['phones' , 'emails'], $location);
        return $location;
        
    }

    public function delete($id){
        $location = parent::delete($id);
        return $location;
    }

    

    private function storeRelations($data , $relations , $location){
            
        foreach($relations as $relation){   
            if (!method_exists($location, $relation)) {
                continue; 
            }
            $location->$relation()->delete();        
            if (!empty($data[$relation]) && is_array($data[$relation])) {
                foreach ($data[$relation] as $item) {      
                    $location->$relation()->create([
                        Str::singular($relation) => $item
                    ]);
                }
            } 

        } // end loop of realtions

        return $location;
                

    }








    
    

    
}