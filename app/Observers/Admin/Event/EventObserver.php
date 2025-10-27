<?php

namespace App\Observers\Admin\Event;

use App\Models\Api\Admin\Event;
use App\Traits\HandlesImage;

class EventObserver
{
    use HandlesImage;
    
    public function updating(Event $event): void
    {
            
        if ($event->isDirty('breadcrumb')) {
            $oldImage = $event->getOriginal('breadcrumb');
            $this->deleteImage($oldImage);
        }  
        
        if ($event->isDirty('event_image')) {
            $oldImage = $event->getOriginal('event_image');
            $this->deleteImage($oldImage);
        } 


    }


    public function deleting(Event $event): void
    {
        $this->deleteImage($event->breadcrumb);
        $this->deleteImage($event->event_image);      
    }

    

    
}