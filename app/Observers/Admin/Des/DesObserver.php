<?php

namespace App\Observers\Admin\Des;

use App\Models\Api\Admin\Des;
use App\Traits\HandlesImage;

class DesObserver
{
    use HandlesImage;
    public function updating(Des $des): void
    {
            
        if ($des->isDirty('breadcrumb')) {
            $oldImage = $des->getOriginal('breadcrumb');
            $this->deleteImage($oldImage);

        }

        if ($des->isDirty('des_image')) {
            $oldImage = $des->getOriginal('des_image');
            $this->deleteImage($oldImage);

        }

        
    }


    public function deleting(Des $des): void
    {
        $this->deleteImage($des->breadcrumb);
        $this->deleteImage($des->des_image);
        
    }


    

    
}