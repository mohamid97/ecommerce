<?php

namespace App\Observers\Admin\Blog;

use App\Models\Api\Admin\Blog;
use App\Traits\HandlesImage;

class BlogObserver
{
    use HandlesImage;

    public function updating(Blog $blog): void
    {
            
        if ($blog->isDirty('blog_image')) {
            $oldImage = $blog->getOriginal('blog_image');
            $this->deleteImage($oldImage);

        }

        if ($blog->isDirty('breadcrumb')) {
            $oldImage = $blog->getOriginal('breadcrumb');
            $this->deleteImage($oldImage);

        }
        
    }


    public function deleting(Blog $blog): void
    {
        $this->deleteImage($blog->blog_image);
        $this->deleteImage($blog->breadcrumb);
        
    }
    
    
}