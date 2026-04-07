<?php
namespace App\Services\Admin\Ecommerce\Shipment;

use App\Models\Api\Ecommerce\ShipmentCity;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;
class CityService extends BaseModelService{
       use StoreMultiLang;
    protected string $modelClass = ShipmentCity::class;

    public function all($request){
        $city = ($this->data['zone_id']) ? ShipmentCity::where('zone_id' , $this->data['zone_id'])->get():  parent::all($request);
        return $city;
    }

    public function view($id){
        $cityDetails = parent::view($id);
        return $cityDetails;
    }

    public function store()
    {
  
        $city = parent::store($this->getBasicColumn(['status' , 'zone_id']));
        $this->processTranslations($city, $this->data, ['title', 'des']);  
        return $city;
        
    }
    


    public function update($id ){

        $city = parent::update($id , $this->getBasicColumn(['status' , 'zone_id']));
        $this->processTranslations($city, $this->data, ['title', 'des']);
        return $city;
        
    }

    public function delete($id){
        $city = parent::delete($id);
        return $city;
    }


    public function applySearch(Builder $query, string $search){
        return $query->where(function ($q) use ($search) {
            $q->whereTranslationLike('title', "%$search%");
        });
    }

    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }
}