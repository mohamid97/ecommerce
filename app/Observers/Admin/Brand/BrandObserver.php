<?php

namespace App\Observers\Admin\Brand;

use App\Models\Api\Admin\Brand;
use App\Traits\HandlesImage;

class BrandObserver
{
        use HandlesImage;


    public function updating(Brand $brand): void
    {
            
        if ($brand->isDirty('image')) {
            $oldImage = $brand->getOriginal('category_image');
            $this->deleteImage($oldImage);

        }


        if ($brand->isDirty('breadcrumb')) {
            $oldImage = $brand->getOriginal('breadcrumb');
            $this->deleteImage($oldImage);

        }

        
    }


    public function deleting(Brand $brand): void
    {
        $this->deleteImage($brand->category_image);
        $this->deleteImage($brand->breadcrumb);
        
    }

    
}