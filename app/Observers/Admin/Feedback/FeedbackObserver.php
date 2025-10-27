<?php

namespace App\Observers\Admin\Feedback;

use App\Models\Api\Admin\Feedback;
use App\Traits\HandlesImage;

class FeedbackObserver
{
    use HandlesImage;
        public function updating(Feedback $feedback): void
    {
            
        if ($feedback->isDirty('breadcrumb')) {
            $oldImage = $feedback->getOriginal('breadcrumb');
            $this->deleteImage($oldImage);

        }

       if ($feedback->isDirty('feedback_image')) {
            $oldImage = $feedback->getOriginal('feedback_image');
            $this->deleteImage($oldImage);

        }


        
    }


    public function deleting(Feedback $feedback): void
    {
        $this->deleteImage($feedback->feedback_image);
        $this->deleteImage($feedback->breadcrumb);
      
    }
    
}