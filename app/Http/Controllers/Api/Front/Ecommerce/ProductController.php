<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Admin\IndustryResource;
use App\Http\Resources\Api\Front\Ecommerce\ProductDetailsResource;
use App\Http\Resources\Api\Front\Ecommerce\ProductNoOptionResource;
use App\Http\Resources\Api\Front\Ecommerce\ProductResource;
use App\Http\Resources\Api\Front\Ecommerce\LastpieceResource;
use App\Http\Resources\Api\Front\Ecommerce\NewestResource;
use App\Http\Resources\Api\Front\Ecommerce\VaraintDetailsResource;
use App\Services\Ecommerce\Product\ProductService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ResponseTrait;

    public function __construct(private ProductService $service) {}

    /**
     * Get list of products with filters, sorting, and pagination.
     */
    public function get(Request $request)
    {
        try {
            $products = $this->service->getProducts($request->all());

            return $this->successPaginated(
                $products,
                ProductResource::collection($products),
                'products',
                __('main.list_successfully', ['products' => 'Products'])
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Get detailed information for a single product.
     */
    public function productDetails(Request $request)
    {
        try {
            $product = $this->service->getProductDetails($request->id);

            if (!$product->has_options) {
                return $this->success(new ProductNoOptionResource($product), __('main.show_successfully', ['model' => 'Product']));
            }

            return $this->success(new ProductDetailsResource($product), __('main.show_successfully', ['model' => 'Product']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Get last piece products.
     */
    public function lastPiece(Request $request)
    {
        try {
            $products = $this->service->getLastPieceProducts($request->all());

            return $this->successPaginated(
                $products,
                LastpieceResource::collection($products),
                'last_piece_products',
                __('main.list_successfully', ['model' => 'Products'])
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Get newest products.
     */
    public function newest(Request $request)
    {
        try {
            $products = $this->service->getNewestProducts($request->all());

            return $this->successPaginated(
                $products,
                NewestResource::collection($products),
                'newest_products',
                __('main.list_successfully', ['model' => 'Products'])
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Get list of industries.
     */
    public function industries(Request $request)
    {
        try {
            $industries = $this->service->getIndustries($request->all());

            return $this->successPaginated(
                $industries,
                IndustryResource::collection($industries),
                'industries',
                __('main.list_successfully', ['model' => 'Industries'])
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Get products by industry ID.
     */
    public function productsByIndustry(Request $request)
    {
        if (!$request->has('id')) {
            return $this->error(__('main.no_id'), 404);
        }

        try {
            $products = $this->service->getProductsByIndustry($request->id, $request->all());

            return $this->successPaginated(
                $products,
                ProductResource::collection($products),
                'products',
                __('main.list_successfully', ['model' => 'Products'])
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Get variant details.
     */
    public function varaintDetails(Request $request)
    {
        if (!$request->has('variant_id')) {
            return $this->error(__('main.no_id'), 400);
        }

        try {
            $variant = $this->service->getVariantDetails($request->variant_id);

            return $this->success(new VaraintDetailsResource($variant), __('main.show_successfully', ['model' => 'Variant']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Get related products based on a product's brand, category, or industry.
     */
    public function relatedProducts(Request $request)
    {
        if (!$request->has('id')) {
            return $this->error(__('main.no_id'), 404);
        }

        try {
            $relatedProducts = $this->service->getRelatedProducts($request->id, $request->all());

            return $this->successPaginated(
                $relatedProducts,
                ProductResource::collection($relatedProducts),
                'related_products',
                __('main.list_successfully', ['model' => 'Related Products'])
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 404);
        }
    }


    
}
