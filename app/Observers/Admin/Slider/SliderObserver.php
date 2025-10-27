<?php
namespace App\Observers\Admin\Slider;
use App\Models\Api\Admin\Slider;
use Illuminate\Support\Facades\File;
use App\Traits\HandlesImage;

class SliderObserver
{
    use HandlesImage;
    public function deleting(Slider $slider): void
    {
        
        $this->deleteImage($slider->image);
    }

     public function updating(Slider $slider): void
    {
        // Only delete the old image if a new one is uploaded
        if ($slider->isDirty('image')) {
            $oldImage = $slider->getOriginal('image');
            $this->deleteImage($oldImage);

        }

        
    }



    

    
}