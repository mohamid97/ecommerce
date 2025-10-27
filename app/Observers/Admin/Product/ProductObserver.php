<?php

namespace App\Observers\Admin\Product;

use App\Models\Api\Admin\Product;
use App\Traits\HandlesImage;

class ProductObserver
{
    use HandlesImage;
    public function updating(Product $product): void
    {
            
        if ($product->isDirty('product_image')) {
            $oldImage = $product->getOriginal('product_image');
            $this->deleteImage($oldImage);

        }

        if ($product->isDirty('breadcrumb')) {
            $oldImage = $product->getOriginal('breadcrumb');
            $this->deleteImage($oldImage);

        }
        
    }


    public function deleting(Product $product): void
    {
        $this->deleteImage($product->service_image);
        $this->deleteImage($product->breadcrumb);
        
    }

    

    
}