<?php
namespace App\Services\Admin\Ecommerce\Shipment;

use App\Models\Api\Ecommerce\ShipmentWayZone;
use App\Services\BaseModelService;
use Illuminate\Database\Eloquent\Builder;

class ShipmentWayZoneService extends BaseModelService
{
    protected string $modelClass = ShipmentWayZone::class;
    protected array $relations = ['way', 'zone'];

    public function all($request)
    {
        if (!empty($request['way_id'])) {
            return ShipmentWayZone::with($this->relations)->where('way_id', $request['way_id'])->get();
        }

        return parent::all($request);
    }

    public function view($id)
    {
        return parent::view($id);
    }

    public function store()
    {
        return parent::store($this->getBasicColumn(['way_id', 'zone_id', 'price', 'status']));
    }

    public function update($id)
    {
        return parent::update($id, $this->getBasicColumn(['way_id', 'zone_id', 'price', 'status']));
    }

    public function delete($id)
    {
        return parent::delete($id);
    }

    public function applySearch(Builder $query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('way', function ($w) use ($search) {
                $w->whereTranslationLike('title', "%$search%");
            })->orWhereHas('zone', function ($z) use ($search) {
                $z->whereTranslationLike('title', "%$search%");
            });
        });
    }

    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }
}