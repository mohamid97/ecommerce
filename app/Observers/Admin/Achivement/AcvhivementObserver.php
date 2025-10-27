<?php

namespace App\Observers\Admin\Achivement;

use App\Models\Api\Admin\Achivement;
use App\Traits\HandlesImage;

class AcvhivementObserver
{
    use HandlesImage;
    public function updating(Achivement $achivement): void
    {
            
        if ($achivement->isDirty('breadcrumb')) {
            $oldImage = $achivement->getOriginal('breadcrumb');
            $this->deleteImage($oldImage);

        }

        if ($achivement->isDirty('achivement_image')) {
            $oldImage = $achivement->getOriginal('achivement_image');
            $this->deleteImage($oldImage);

        }

        
    }


    public function deleting(Achivement $achivement): void
    {
        $this->deleteImage($achivement->breadcrumb);
        $this->deleteImage($achivement->achivement_image);
        
    }

    
}