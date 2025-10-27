<?php

namespace App\Services\Admin\Feedback;

use App\Models\Api\Admin\Feedback;
use App\Services\BaseModelService;
use App\Traits\HandlesImage;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;

class FeedbackService extends BaseModelService{
    use StoreMultiLang , HandlesImage;
    
    protected string $modelClass = Feedback::class;

    



    public function all($request){
        $feedback = parent::all($request);
        return $feedback;
    }

    public function view($id){
        $details = parent::view($id);
        return $details;
    }

    public function store()
    {

        $this->uploadSingleImage(['breadcrumb' , 'feedback_image'], 'uploads/feedbacks');  
        $feed = parent::store($this->getBasicColumn(['feedback_image','breadcrumb']));
        $this->processTranslations($feed, $this->data, ['title', 'small_des' ,'des','meta_des' , 'meta_title']);  
        return $feed;
        
    }
    


    public function update($id){ 

        $this->uploadSingleImage(['breadcrumb' , 'feedback_image'], 'uploads/feedbacks');  
        $feed = parent::update($id , $this->getBasicColumn(['feedback_image','breadcrumb']));
        $this->processTranslations($feed, $this->data, ['title', 'small_des' ,'des','meta_des' , 'meta_title']);
        return $feed;
        
    }

    public function delete($id){
        $feed = parent::delete($id);
        return $feed;
        
    }


    public function applySearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('des', 'like', "%{$search}%")
                  ->orWhere('small_des' , 'like' , "%{$search}%");
            });
        });
        
    }


    public function orderBy(Builder $query, string $orderBy, string $direction)
    {
        return $query->orderBy($orderBy, $direction);
    }

    
    
    

}