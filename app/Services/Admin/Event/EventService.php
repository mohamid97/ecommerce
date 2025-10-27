<?php
namespace App\Services\Admin\Event;

use App\Models\Api\Admin\Event;
use App\Services\BaseModelService;
use App\Traits\HandlesImage;
use App\Traits\StoreMultiLang;

class EventService extends BaseModelService{

    use StoreMultiLang , HandlesImage;
    protected string $modelClass = Event::class;


    public function all($request){
        $eventDetails = parent::all($request);
        return $eventDetails;
    }

    public function view($id){
        $eventDetails = parent::view($id);
        return $eventDetails;
    }

    public function store()
    {
        $this->uploadSingleImage(['event_image' , 'breadcrumb' ], 'uploads/events');
        $event = parent::store($this->getBasicColumn(['breadcrumb', 'date' , 'event_image']));
        $this->data['slug']  = $this->createSlug($this->data);
        $this->processTranslations($event, $this->data, ['title','slug', 'des' ,'meta_title' , 'meta_des', 'alt_image' , 'title_image']);
        return $event;


    }

    public function update($id ){
        $this->uploadSingleImage(['event_image' , 'breadcrumb' ], 'uploads/events');
        $event = parent::update($id , $this->getBasicColumn(['breadcrumb', 'date' , 'event_image']));
        $this->processTranslations($event, $this->data, ['title','slug', 'des' , 'meta_title' , 'meta_des' , 'alt_image' , 'title_image']);
        return $event;

    }

    public function delete($id){
        $event = parent::delete($id);
        return $event;

    }




}
