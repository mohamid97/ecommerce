<?php

namespace App\Services\Admin\Ecommerce\Bundel\Actions;

use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\BundelDetails;
use App\Models\Api\Ecommerce\ProductVariant;
use Exception;

class CheckBundleProductsStatusAction
{
    public function execute(Bundel $bundel): void
    {
        $bundleDetails = $bundel->bundelDetails;
    //    dd($bundleDetails);
        foreach ($bundleDetails as $bundleDetail) {

            // the product can has varant
            if ($bundleDetail->variant_ids != null) {
                $variants = ProductVariant::whereIn('id', $bundleDetail->variant_ids)->get();
                foreach ($variants as $variant) {
                    if ($variant->status != 'active') {
                        throw new Exception(__('main.variant_not_active'));
                    }
                }
            } else {
                $product = $bundleDetail->product;
                if ($product->status != 'active') {
                    throw new Exception(__('main.product_not_active'));
                }
            }
        }
    }
}
