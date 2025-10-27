<?php

namespace App\Observers\Admin\Mediaimage;

use App\Models\Api\Admin\Mediaimage;
use App\Traits\HandlesImage;

class MediaimageObserver
{
    use HandlesImage;
    public function deleting(Mediaimage $image): void
    {
        
        $this->deleteImage($image->image);
    }

    public function updating(Mediaimage $image): void
    {
        if ($image->isDirty('image')) {
            $oldImage = $image->getOriginal('image');
            $this->deleteImage($oldImage);

        }
        
    }


    
}