<?php
namespace App\Services\Admin\Client;

use App\Models\Api\Admin\Client;
use App\Services\BaseModelService;
use App\Traits\HandlesImage;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;
class ClientService extends BaseModelService{

    use StoreMultiLang , HandlesImage;
    protected string $modelClass = Client::class;
    public function all($request){
        $clients = parent::all($request);
        return $clients;
    }

    public function view($id){
        $details = parent::view($id);
        return $details;
    }

    public function store()
    {

        $this->uploadSingleImage(['image','breadcrumb'], 'uploads/clients');
        $client = parent::store($this->getBasicColumn(['image' , 'breadcrumb' , 'type' , 'link']));
        $this->processTranslations($client, $this->data, ['title', 'des','alt_image' , 'title_image']);  
        return $client;
        
    }
    
    public function update($id){
        $this->uploadSingleImage(['image','breadcrumb'], 'uploads/clients');
        $client = parent::update($id , $this->getBasicColumn(['image' , 'breadcrumb' , 'type','link']));
        $this->processTranslations($client, $this->data, ['title', 'des' , 'alt_image' , 'title_image']);
        return $client;
        
    }

    public function delete($id){
        $client = Client::findOrFail($id);
        $this->deleteImage($client->image);
        $event = parent::delete($id);
        return $client;
    }

    public function applySearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('des', 'like', "%{$search}%");
            });
        });
    }


     public function orderBy(Builder $query, string $orderBy, string $direction)
    {
        return $query->orderBy($orderBy, $direction);
    }

    public function type(Builder $query , $type){
        return $query->where('type', $type);
    }



    
}