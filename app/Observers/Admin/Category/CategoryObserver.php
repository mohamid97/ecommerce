<?php

namespace App\Observers\Admin\Category;

use App\Models\Api\Admin\Category;
use App\Traits\HandlesImage;

class CategoryObserver
{
    use HandlesImage;


    public function updating(Category $category): void
    {
            
        if ($category->isDirty('image')) {
            $oldImage = $category->getOriginal('category_image');
            $this->deleteImage($oldImage);

        }

        if ($category->isDirty('thumbnail')) {
            $oldImage = $category->getOriginal('thumbnail');
            $this->deleteImage($oldImage);

        }

        if ($category->isDirty('breadcrumb')) {
            $oldImage = $category->getOriginal('breadcrumb');
            $this->deleteImage($oldImage);

        }

        
    }


    public function deleting(Category $category): void
    {
        $this->deleteImage($category->category_image);
        $this->deleteImage($category->thumbnail);
        $this->deleteImage($category->breadcrumb);
        
    }


    
    


}