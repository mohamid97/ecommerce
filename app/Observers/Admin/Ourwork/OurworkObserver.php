<?php

namespace App\Observers\Admin\Ourwork;

use App\Models\Api\Admin\Ourwork;
use App\Traits\HandlesImage;

class OurworkObserver
{
    use HandlesImage;
    public function updating(Ourwork $ourwork): void
    {
            
        if ($ourwork->isDirty('breadcrumb')) {
            $oldImage = $ourwork->getOriginal('breadcrumb');
            $this->deleteImage($oldImage);

        }

        
    }


    public function deleting(Ourwork $ourwork): void
    {
         $this->deleteImage($ourwork->breadcrumb);
     
        
    }
    
}