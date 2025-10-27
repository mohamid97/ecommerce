<?php

namespace App\Observers\Admin\Page;

use App\Models\Api\Admin\Page;
use App\Traits\HandlesImage;

class PageObserver
{
    use HandlesImage;
    public function deleting(Page $page): void
    {
        
        $this->deleteImage($page->page_image);
    }

     public function updating(Page $page): void
    {
        // Only delete the old image if a new one is uploaded
        if ($page->isDirty('page_image')) {
            $oldImage = $page->getOriginal('page_image');
            $this->deleteImage($oldImage);

        }

        
    }
}