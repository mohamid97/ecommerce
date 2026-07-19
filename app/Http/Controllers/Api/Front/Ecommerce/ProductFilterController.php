<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Front\Ecommerce\ProductResource;
use App\Services\Ecommerce\Product\ProductFilterService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ProductFilterController extends Controller
{
    use ResponseTrait;

    public function __construct(private ProductFilterService $service) {}

    /**
     * Get filtered products with available filters.
     * Returns products and filter options with counts (like Amazon).
     */
    public function filter(Request $request)
    {
        try {
            $result = $this->service->getFilteredProducts($request->all());

            return response()->json([
                'success' => true,
                'message' => __('main.list_successfully', ['model' => 'Products']),
                'data' => [
                    'items' => ProductResource::collection($result['products']),
                    'pagination' => [
                        'total' => $result['products']->total(),
                        'per_page' => $result['products']->perPage(),
                        'current_page' => $result['products']->currentPage(),
                        'last_page' => $result['products']->lastPage(),
                    ],
                ],
                'filters' => $result['filters'],
                'price_range' => $result['price_range'],
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
