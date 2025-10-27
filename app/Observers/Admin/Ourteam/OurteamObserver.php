<?php

namespace App\Observers\Admin\Ourteam;

use App\Models\Api\Admin\Ourteam;
use App\Traits\HandlesImage;

class OurteamObserver
{
    use HandlesImage;
    
    public function deleting(Ourteam $ourteam): void
    {
        
        $this->deleteImage($ourteam->image);
    }

     public function updating(Ourteam $ourteam): void
    {
        if ($ourteam->isDirty('image')) {
            $oldImage = $ourteam->getOriginal('image');
            $this->deleteImage($oldImage);

        }

        
    }
}