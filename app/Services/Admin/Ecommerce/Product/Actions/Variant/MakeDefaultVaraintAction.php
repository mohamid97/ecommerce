<?php

namespace App\Services\Admin\Ecommerce\Product\Actions\Variant;

use App\Models\Api\Ecommerce\ProductVariant;

class MakeDefaultVaraintAction
{
    /**
     * Make the specified product variant the default.
     *
     * @param  int  $variant_id
     * @return \App\Models\Api\Ecommerce\ProductVariant
     * @throws \Exception
     */
    public function makeDefault($variant_id)
    {
        $variant = ProductVariant::find($variant_id);

        if (!$variant) {
            throw new \Exception(__('main.model_not_found_id', ['model' => 'Variant', 'id' => $variant_id]));
        }

        ProductVariant::where('product_id', $variant->product_id)
            ->where('is_default', true)
            ->update(['is_default' => false]);

        $variant->update(['is_default' => true]);

        return $variant;
    }
}
