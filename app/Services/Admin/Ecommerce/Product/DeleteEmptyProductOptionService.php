<?php
namespace App\Services\Admin\Ecommerce\Product;

use App\Models\Api\Ecommerce\ProductOption;

class DeleteEmptyProductOptionService
{

// This function deletes product options that have no values associated with them
    public function deleteEmptyOptions($productId)
    {
        $productOptions = ProductOption::where('product_id', $productId)->get();

        foreach ($productOptions as $productOption) {
            if ($productOption->values()->count() == 0) {
                $productOption->delete();
            }
        }
    } // end of deleteEmptyOptions function



    
}