<?php

namespace App\Services\Admin\Ecommerce\Product\Actions\Variant;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Bundel;

class FilterProductaraintsAction
{
    /**
     * Filter products and bundles based on search criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function filter($request)
    {
        $filter = [];

        // Search for product
        $filter[] = Product::with('variants')->whereHas('translation', function ($query) use ($request) {
            $query->where('title', 'like', '%' . $request->search . '%');
        })->get();

        // Search for bundle
        if ($request->has('type') && $request->type == 'all') {
            $filter[] = Bundel::with([
                'bundelDetails.product'
            ])->whereHas('translation', function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%');
            })->get();
        }

        // Merge collections and remove duplicates
        return collect($filter)->flatten()->values();
    }
}
