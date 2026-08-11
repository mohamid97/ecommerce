<?php
namespace App\Services\Admin\Ecommerce\Shipment;

use App\Models\Api\Ecommerce\ShipmentWay;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;
class ShipmentWayService extends BaseModelService{
    use StoreMultiLang;
    protected string $modelClass = ShipmentWay::class;

    public function all($request){
        $way = parent::all($request);
        return $way;
    }

    public function view($id){
        $wayDetails = parent::view($id);
        return $wayDetails;
    }

    public function store()
    {
        $way = parent::store($this->getBasicColumn(['status', 'capacity']));
        $this->processTranslations($way, $this->data, ['title', 'des']);
        return $way;
    }

    public function update($id){
        $way = parent::update($id, $this->getBasicColumn(['status', 'capacity']));
        $this->processTranslations($way, $this->data, ['title', 'des']);
        return $way;
    }

    public function delete($id){
        $way = parent::delete($id);
        return $way;
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