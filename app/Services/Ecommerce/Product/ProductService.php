<?php

namespace App\Services\Ecommerce\Product;

use App\Models\Api\Admin\Industry;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\LastPiece;
use App\Models\Api\Ecommerce\NewProduct;
use App\Models\Api\Ecommerce\ProductVariant;
use Exception;

class ProductService
{
    /**
     * Get products list with filters, sorting, and pagination.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getProducts(array $data)
    {
        $products = Product::with(['category', 'brand', 'industries']);

        if (!empty($data['search'])) {
            $products->whereHas('translations', function($query) use ($data) {
                $query->where('title', 'like', '%' . $data['search'] . '%');
            });
        }

        if (!empty($data['category_id'])) {
            $products->where('category_id', $data['category_id']);
        }

        if (!empty($data['industry_id'])) {
            $products->whereHas('industries', function ($query) use ($data) {
                $query->where('industries.id', $data['industry_id']);
            });
        }

        if (!empty($data['sort']) && in_array($data['sort'], ['asc', 'desc'])) {
            if (!empty($data['sort_by']) && in_array($data['sort_by'], ['created_at', 'sale_price', 'id', 'discount', 'order'])) {
                $products->orderBy($data['sort_by'], $data['sort']);
            } else {
                $products->orderBy('created_at', $data['sort']);
            }
        }

        $products->where('status', '!=', 'draft');
        $paginate = (!empty($data['paginate']) && ($data['paginate'] >= 1 && $data['paginate'] <= 100)) ? $data['paginate'] : 10;

        return $products->paginate($paginate);
    }

    /**
     * Get single product details.
     *
     * @param  int  $id
     * @return \App\Models\Api\Admin\Product
     * @throws \Exception
     */
    public function getProductDetails($id)
    {
        $product = Product::with(['category', 'brand', 'industries'])
            ->where('status', '!=', 'draft')
            ->where('id', $id)
            ->first();

        if (!$product) {
            throw new Exception(__('main.not_found', ['model' => 'Product']));
        }

        if ($product->has_options) {
            $product->load([
                'options.option',
                'options.values.optionValue',
                'variants.variants.optionValue.option',
                'variants.varaintImages.image',
            ]);
        }

        return $product;
    }

    /**
     * Get last piece products list.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getLastPieceProducts(array $data)
    {
        $subQuery = LastPiece::query()->select('product_id');
        return $this->getSectionProducts($data, $subQuery);
    }

    /**
     * Get newest products list.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getNewestProducts(array $data)
    {
        $subQuery = NewProduct::query()->select('product_id');
        return $this->getSectionProducts($data, $subQuery);
    }

    /**
     * Get list of industries.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getIndustries(array $data)
    {
        $industries = Industry::query();

        if (!empty($data['search'])) {
            $industries->whereHas('translations', function ($query) use ($data) {
                $query->where('title', 'like', '%' . $data['search'] . '%');
            });
        }

        if (!empty($data['sort']) && in_array($data['sort'], ['asc', 'desc'])) {
            $industries->orderBy('created_at', $data['sort']);
        }

        $paginate = (!empty($data['paginate']) && ($data['paginate'] >= 1 && $data['paginate'] <= 100)) ? $data['paginate'] : 10;

        return $industries->paginate($paginate);
    }

    /**
     * Get products by industry ID.
     *
     * @param  int  $industry_id
     * @param  array  $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Exception
     */
    public function getProductsByIndustry($industry_id, array $data)
    {
        $industry = Industry::find($industry_id);
        if (!$industry) {
            throw new Exception(__('main.not_found', ['model' => 'Industry']));
        }

        $products = Product::with(['category', 'brand', 'industries'])
            ->where('status', '!=', 'draft')
            ->whereHas('industries', function ($query) use ($industry_id) {
                $query->where('industries.id', $industry_id);
            });

        if (!empty($data['sort']) && in_array($data['sort'], ['asc', 'desc'])) {
            $products->orderBy('created_at', $data['sort']);
        }

        $paginate = (!empty($data['paginate']) && ($data['paginate'] >= 1 && $data['paginate'] <= 100)) ? $data['paginate'] : 10;

        return $products->paginate($paginate);
    }

    /**
     * Get variant details.
     *
     * @param  int  $variant_id
     * @return \App\Models\Api\Ecommerce\ProductVariant
     * @throws \Exception
     */
    public function getVariantDetails($variant_id)
    {
        $variant = ProductVariant::with(['varaintImages', 'variants'])
            ->where('status', '!=', 'draft')
            ->where('id', $variant_id)
            ->first();

        if (!$variant) {
            throw new Exception(__('main.not_found', ['model' => 'Variant']));
        }

        return $variant;
    }

    /**
     * Get related products based on a product's brand, category, or industry.
     *
     * @param  int  $product_id
     * @param  array  $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Exception
     */
    public function getRelatedProducts($product_id, array $data)
    {
        $product = Product::find($product_id);
        if (!$product) {
            throw new Exception(__('main.not_found', ['model' => 'Product']));
        }

        $relatedProducts = Product::with(['category', 'brand', 'industries'])
            ->where('status', '!=', 'draft')
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id)
                    ->orWhere('brand_id', $product->brand_id)
                    ->orWhereHas('industries', function ($query) use ($product) {
                        $query->whereIn('industries.id', $product->industries->pluck('id'));
                    });
            });

        $paginate = (!empty($data['paginate']) && ($data['paginate'] >= 1 && $data['paginate'] <= 100)) ? $data['paginate'] : 10;

        return $relatedProducts->paginate($paginate);
    }

    /**
     * Helper to get section products (e.g. LastPiece, NewProduct).
     *
     * @param  array  $data
     * @param  mixed  $subQuery
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getSectionProducts(array $data, $subQuery)
    {
        $products = Product::query()
            ->with(['category', 'brand', 'industries'])
            ->where('status', '!=', 'draft')
            ->whereIn('id', $subQuery);

        $paginate = (!empty($data['paginate']) && ($data['paginate'] >= 1 && $data['paginate'] <= 100)) ? $data['paginate'] : 10;

        return $products->paginate($paginate);
    }
}
