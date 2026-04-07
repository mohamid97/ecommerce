<?php
namespace App\Services\Admin\Ecommerce\Shipment;

use App\Models\Api\Ecommerce\ShipmentZone;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;
class ZoneService extends BaseModelService{
   use StoreMultiLang;
    protected string $modelClass = ShipmentZone::class;

        public function all($request){
        $zone = parent::all($request);
        return $zone;
    }

    public function view($id){
        $zoneDetails = parent::view($id);
        return $zoneDetails;
    }

    public function store()
    {
  
        $zone = parent::store($this->getBasicColumn(['status' , 'price']));
        $this->processTranslations($zone, $this->data, ['title', 'des']);  
        return $zone;
        
    }
    


    public function update($id ){

        $zone = parent::update($id , $this->getBasicColumn(['status' , 'price']));
        $this->processTranslations($zone, $this->data, ['title', 'des']);
        return $zone;
        
    }

    public function delete($id){
        $zone = parent::delete($id);
        return $zone;
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