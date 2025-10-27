<?php

namespace App\Observers\Admin\Service;

use App\Models\Api\Admin\Service;
use App\Traits\HandlesImage;

class ServiceObserver
{
    use HandlesImage;
    public function updating(Service $service): void
    {
            
        if ($service->isDirty('service_image')) {
            $oldImage = $service->getOriginal('service_image');
            $this->deleteImage($oldImage);

        }

        if ($service->isDirty('breadcrumb')) {
            $oldImage = $service->getOriginal('breadcrumb');
            $this->deleteImage($oldImage);

        }
        
    }


    public function deleting(Service $service): void
    {
        $this->deleteImage($service->service_image);
        $this->deleteImage($service->breadcrumb);
        
    }


    
}