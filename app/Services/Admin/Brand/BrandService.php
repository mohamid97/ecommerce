<?php

namespace  App\Services\Admin\Brand;

use App\Models\Api\Admin\Brand as AdminBrand;
use App\Services\BaseModelService;
use App\Traits\HandlesImage;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;

class BrandService extends BaseModelService{
    use StoreMultiLang , HandlesImage;
    protected string $modelClass = AdminBrand::class;
    
    public function all($request){
        $brands = parent::all($request);
        return $brands;
    }

    
    public function view($id){
        $brandDetails = parent::view($id);
        return $brandDetails;
    }

    
    public function store()
    {
  
        $this->uploadSingleImage(['image' , 'breadcrumb'] , 'uploads/brand');
        $this->data['slug']  = $this->createSlug($this->data);
        $brand = parent::store($this->getBasicColumn(['breadcrumb' , 'image','link']));
        $this->processTranslations($brand, $this->data, ['title' ,'des','slug' , 'meta_title' , 'meta_des', 'alt_image' , 'title_image']);  
        return $brand;
        
    }



    
    public function update($id ){
        $this->uploadSingleImage(['image' , 'breadcrumb'] , 'uploads/brand');
        $brand = parent::update($id , $this->getBasicColumn(['breadcrumb' , 'image','link']));
        $this->processTranslations($brand, $this->data, ['title' ,'des','slug' , 'meta_title' , 'meta_des', 'alt_image' , 'title_image']);  
        return $brand;
        
    }


    public function delete($id){
        $brand = parent::delete($id);
        return $brand;
    }


    

    public function applySearch(Builder $query, string $search ){
        return $query->where(function ($q) use ($search) {
            $q->whereTranslationLike('title', "%$search%")
              ->orWhereTranslationLike('des', "%$search%");
        });
    }
    
    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }

    


    
}