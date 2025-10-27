<?php
namespace App\Services\Admin\Service;

use App\Models\Api\Admin\Service;
use App\Services\BaseModelService;
use App\Traits\HandlesImage;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;
class ServiceService extends BaseModelService{
    
    use StoreMultiLang , HandlesImage;
    protected string $modelClass = Service::class;
    protected array $relations = ['category'];


    public function all($request){

        $serivces = parent::all($request);
        return $serivces;
    }

    public function view($id){
        $serviceDetails = parent::view($id);
        return $serviceDetails;
    }

    public function store()
    {
        $this->uploadSingleImage(['service_image', 'breadcrumb'], 'uploads/services');
        $this->data['slug']  = $this->createSlug($this->data);
        $service = parent::store($this->getBasicColumn([ 'service_image', 'breadcrumb', 'price','category_id','order']));
        $this->processTranslations($service, $this->data, ['title', 'slug' ,'des' , 'small_des' , 'meta_title' , 'meta_des', 'alt_image' , 'title_image']);  
        return $service;
        
    }
    


    public function update($id){
        $this->uploadSingleImage(['service_image', 'breadcrumb'], 'uploads/services');
        $blog = parent::update($id , $this->getBasicColumn(['service_image', 'breadcrumb', 'price','category_id','order']));
        $this->processTranslations($blog, $this->data, ['title', 'slug' ,'des' , 'small_des' , 'meta_title' , 'meta_des', 'alt_image' , 'title_image']);
        return $blog;
        
    }

    

    public function delete($id){
        $service = parent::delete($id);
        return $service;
    }


    public function applySearch(Builder $query, string $search ){
        return $query->where(function ($q) use ($search) {
            $q->whereTranslationLike('title', "%$search%")
              ->orWhereTranslationLike('slug', "%$search%");
        });
    }

    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }


    


}