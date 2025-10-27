<?php
namespace App\Services\Ecommerce\Option;

use App\Models\Api\Ecommerce\Option;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;


class OptionService extends BaseModelService
{
    use StoreMultiLang;
    
    protected string $modelClass = Option::class;
    public function all($request){
        $allDetails = parent::all($request);
        return $allDetails;
    }

    public function view($id){
        $option = parent::view($id);
        return $option;
    }

    public function store()
    {
     
        $this->uploadSingleImage(['option_image'], 'uploads/options'); 
        $option = parent::store($this->getBasicColumn(['option_image']));
        $this->processTranslations($option, $this->data, ['title']);
        return $option;
        
    }
    
    public function update($id){
        $this->uploadSingleImage(['option_image'], 'uploads/options'); 
        $option = parent::update($id , $this->getBasicColumn( ['option_image']));
        $this->processTranslations($option, $this->data, ['title' ]);
        return $option;        
    }   
     
    public function delete($id){
        $option = parent::delete($id);
        return $option;
    }


    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }


}