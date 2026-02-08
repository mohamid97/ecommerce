<?php
namespace App\Services\Admin\Ecommerce\Product\Actions\Variant;

use App\Models\Api\Ecommerce\ProductVariant;

class DeleteVaraintAction
{
    public function deleteVariant($variant_id){
        $productVaraint = ProductVariant::findOrFail($variant_id);
        $productVaraint->delete();
    }
}