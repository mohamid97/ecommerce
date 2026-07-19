<?php

namespace App\Services\Ecommerce\Product;

use App\Models\Api\Admin\Brand;
use App\Models\Api\Admin\Category;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Option;
use App\Models\Api\Ecommerce\OptionValue;
use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\VariantOptionValue;
use Illuminate\Support\Facades\DB;

class ProductFilterService
{
    /**
     * Get filtered products with available filter options.
     *
     * @param array $data
     * @return array
     */
    public function getFilteredProducts(array $data): array
    {
        $query = Product::with(['category', 'brand', 'industries', 'variants' => function ($query) {
            $query->where('status', '!=', 'draft')->orderByDesc('is_default')->orderBy('id');
        }]);

        // Base filters
        $this->applyBaseFilters($query, $data);

        // Get the filtered products
        $paginate = (!empty($data['paginate']) && ($data['paginate'] >= 1 && $data['paginate'] <= 100)) ? $data['paginate'] : 10;
        $products = $query->paginate($paginate);

        // Get available filters with counts based on current product set
        $filters = $this->getAvailableFilters($products->items(), $data);

        // Get price range
        $priceRange = $this->getPriceRange($products->items());

        return [
            'products' => $products,
            'filters' => $filters,
            'price_range' => $priceRange,
        ];
    }

    /**
     * Apply base filters to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $data
     * @return void
     */
    private function applyBaseFilters($query, array $data): void
    {
        // Search filter
        if (!empty($data['search'])) {
            $query->whereHas('translations', function ($q) use ($data) {
                $q->where('title', 'like', '%' . $data['search'] . '%');
            });
        }

        // Category filter
        if (!empty($data['category_id'])) {
            $query->where('category_id', $data['category_id']);
        }

        if (!empty($data['category_slug'])) {
            $query->whereHas('category.translations', function ($q) use ($data) {
                $q->where('slug', $data['category_slug']);
            });
        }

        // Brand filter
        if (!empty($data['brand_id'])) {
            $query->where('brand_id', $data['brand_id']);
        }

        if (!empty($data['brand_slug'])) {
            $query->whereHas('brand.translations', function ($q) use ($data) {
                $q->where('slug', $data['brand_slug']);
            });
        }

        // Industry filter
        if (!empty($data['industry_id'])) {
            $query->whereHas('industries', function ($q) use ($data) {
                $q->where('industries.id', $data['industry_id']);
            });
        }

        // Price range filter
        $min = null;
        $max = null;
        if (isset($data['from']) && is_numeric($data['from'])) {
            $min = (float) $data['from'];
        }
        if (isset($data['to']) && is_numeric($data['to'])) {
            $max = (float) $data['to'];
        }

        if ($min !== null || $max !== null) {
            $query->where(function ($q) use ($min, $max) {
                // Products without options
                $q->where(function ($q2) use ($min, $max) {
                    $q2->where('has_options', false);
                    if ($min !== null) {
                        $q2->where('sale_price', '>=', $min);
                    }
                    if ($max !== null) {
                        $q2->where('sale_price', '<=', $max);
                    }
                });

                // Products with variants in price range
                $q->orWhereHas('variants', function ($vq) use ($min, $max) {
                    $vq->where('status', '!=', 'draft');
                    if ($min !== null) {
                        $vq->where('sale_price', '>=', $min);
                    }
                    if ($max !== null) {
                        $vq->where('sale_price', '<=', $max);
                    }
                });
            });
        }

        // Option value filter (for filtering by specific option values)
        if (!empty($data['option_values']) && is_array($data['option_values'])) {
            $query->whereHas('variants', function ($q) use ($data) {
                $q->where('status', '!=', 'draft');
                $q->whereHas('variants', function ($vq) use ($data) {
                    $vq->whereIn('option_value_id', $data['option_values']);
                });
            });
        }

        // Only active products
        $query->where('status', 'active');

        // Sorting
        if (!empty($data['sort']) && in_array($data['sort'], ['asc', 'desc'])) {
            if (!empty($data['sort_by']) && in_array($data['sort_by'], ['created_at', 'sale_price', 'id', 'order'])) {
                $query->orderBy($data['sort_by'], $data['sort']);
            } else {
                $query->orderBy('created_at', $data['sort']);
            }
        }
    }

    /**
     * Get available filters with product counts.
     *
     * @param array $products
     * @param array $currentFilters
     * @return array
     */
    private function getAvailableFilters(array $products, array $currentFilters): array
    {
        $productIds = collect($products)->pluck('id')->toArray();

        // If no products, return empty filters
        if (empty($productIds)) {
            return $this->getEmptyFilters();
        }

        $filters = [];

        // Get all option values for these products
        $optionValues = OptionValue::with('option')
            ->whereHas('productOptionValues.productOption', function ($q) use ($productIds) {
                $q->whereIn('product_id', $productIds);
            })
            ->orWhereHas('variantOptionValues.productVariant.product', function ($q) use ($productIds) {
                $q->whereIn('id', $productIds);
            })
            ->get();

        // Group by option
        $groupedOptions = $optionValues->groupBy('option_id');

        foreach ($groupedOptions as $optionId => $values) {
            $option = $values->first()->option;
            
            $filterValues = $values->map(function ($value) use ($productIds, $currentFilters) {
                $count = $this->getProductCountByOptionValue($value->id, $productIds, $currentFilters);
                
                return [
                    'id' => $value->id,
                    'title' => $value->title,
                    'value' => $value->value,
                    'count' => $count,
                ];
            })->filter(function ($item) {
                return $item['count'] > 0;
            })->values();

            if ($filterValues->isNotEmpty()) {
                $filters[] = [
                    'id' => $option->id,
                    'title' => $option->title,
                    'code' => $option->code,
                    'value_type' => $option->value_type,
                    'values' => $filterValues,
                ];
            }
        }

        // Add categories filter
        $filters['categories'] = $this->getCategoryFilters($productIds, $currentFilters);

        // Add brands filter
        $filters['brands'] = $this->getBrandFilters($productIds, $currentFilters);

        return $filters;
    }

    /**
     * Get product count for a specific option value.
     *
     * @param int $optionValueId
     * @param array $productIds
     * @param array $currentFilters
     * @return int
     */
    private function getProductCountByOptionValue(int $optionValueId, array $productIds, array $currentFilters): int
    {
        $query = Product::whereIn('id', $productIds)
            ->where('status', 'active');

        // Apply other filters except option_values
        if (!empty($currentFilters['category_id'])) {
            $query->where('category_id', $currentFilters['category_id']);
        }

        if (!empty($currentFilters['brand_id'])) {
            $query->where('brand_id', $currentFilters['brand_id']);
        }

        if (!empty($currentFilters['from']) && is_numeric($currentFilters['from'])) {
            $query->where(function ($q) use ($currentFilters) {
                $q->where('has_options', false)
                    ->where('sale_price', '>=', (float) $currentFilters['from']);
                $q->orWhereHas('variants', function ($vq) use ($currentFilters) {
                    $vq->where('status', '!=', 'draft')
                        ->where('sale_price', '>=', (float) $currentFilters['from']);
                });
            });
        }

        if (!empty($currentFilters['to']) && is_numeric($currentFilters['to'])) {
            $query->where(function ($q) use ($currentFilters) {
                $q->where('has_options', false)
                    ->where('sale_price', '<=', (float) $currentFilters['to']);
                $q->orWhereHas('variants', function ($vq) use ($currentFilters) {
                    $vq->where('status', '!=', 'draft')
                        ->where('sale_price', '<=', (float) $currentFilters['to']);
                });
            });
        }

        // Filter by this option value
        $query->where(function ($q) use ($optionValueId) {
            // Products with this option value in their variants
            $q->whereHas('variants', function ($vq) use ($optionValueId) {
                $vq->where('status', '!=', 'draft')
                    ->whereHas('variants', function ($vov) use ($optionValueId) {
                        $vov->where('option_value_id', $optionValueId);
                    });
            });
        });

        return $query->count();
    }

    /**
     * Get category filters with product counts.
     *
     * @param array $productIds
     * @param array $currentFilters
     * @return array
     */
    private function getCategoryFilters(array $productIds, array $currentFilters): array
    {
        $query = Category::whereHas('products', function ($q) use ($productIds) {
            $q->whereIn('products.id', $productIds)
                ->where('status', 'active');
        });

        // Apply other filters
        if (!empty($currentFilters['brand_id'])) {
            $query->whereHas('products', function ($q) use ($productIds, $currentFilters) {
                $q->whereIn('products.id', $productIds)
                    ->where('products.status', 'active')
                    ->where('products.brand_id', $currentFilters['brand_id']);
            });
        }

        $categories = $query->get();

        return $categories->map(function ($category) use ($productIds, $currentFilters) {
            $count = $this->getProductCountByCategory($category->id, $productIds, $currentFilters);
            
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'count' => $count,
            ];
        })->filter(function ($item) {
            return $item['count'] > 0;
        })->values()->toArray();
    }

    /**
     * Get product count for a specific category.
     *
     * @param int $categoryId
     * @param array $productIds
     * @param array $currentFilters
     * @return int
     */
    private function getProductCountByCategory(int $categoryId, array $productIds, array $currentFilters): int
    {
        $query = Product::where('category_id', $categoryId)
            ->where('status', 'active');

        if (!empty($currentFilters['brand_id'])) {
            $query->where('brand_id', $currentFilters['brand_id']);
        }

        if (!empty($currentFilters['from']) && is_numeric($currentFilters['from'])) {
            $query->where(function ($q) use ($currentFilters) {
                $q->where('has_options', false)
                    ->where('sale_price', '>=', (float) $currentFilters['from']);
                $q->orWhereHas('variants', function ($vq) use ($currentFilters) {
                    $vq->where('status', '!=', 'draft')
                        ->where('sale_price', '>=', (float) $currentFilters['from']);
                });
            });
        }

        if (!empty($currentFilters['to']) && is_numeric($currentFilters['to'])) {
            $query->where(function ($q) use ($currentFilters) {
                $q->where('has_options', false)
                    ->where('sale_price', '<=', (float) $currentFilters['to']);
                $q->orWhereHas('variants', function ($vq) use ($currentFilters) {
                    $vq->where('status', '!=', 'draft')
                        ->where('sale_price', '<=', (float) $currentFilters['to']);
                });
            });
        }

        return $query->count();
    }

    /**
     * Get brand filters with product counts.
     *
     * @param array $productIds
     * @param array $currentFilters
     * @return array
     */
    private function getBrandFilters(array $productIds, array $currentFilters): array
    {
        $query = Brand::whereHas('products', function ($q) use ($productIds) {
            $q->whereIn('products.id', $productIds)
                ->where('status', 'active');
        });

        // Apply other filters
        if (!empty($currentFilters['category_id'])) {
            $query->whereHas('products', function ($q) use ($productIds, $currentFilters) {
                $q->whereIn('products.id', $productIds)
                    ->where('products.status', 'active')
                    ->where('products.category_id', $currentFilters['category_id']);
            });
        }

        $brands = $query->get();

        return $brands->map(function ($brand) use ($productIds, $currentFilters) {
            $count = $this->getProductCountByBrand($brand->id, $productIds, $currentFilters);
            
            return [
                'id' => $brand->id,
                'title' => $brand->title,
                'slug' => $brand->slug,
                'count' => $count,
            ];
        })->filter(function ($item) {
            return $item['count'] > 0;
        })->values()->toArray();
    }

    /**
     * Get product count for a specific brand.
     *
     * @param int $brandId
     * @param array $productIds
     * @param array $currentFilters
     * @return int
     */
    private function getProductCountByBrand(int $brandId, array $productIds, array $currentFilters): int
    {
        $query = Product::where('brand_id', $brandId)
            ->where('status', 'active');

        if (!empty($currentFilters['category_id'])) {
            $query->where('category_id', $currentFilters['category_id']);
        }

        if (!empty($currentFilters['from']) && is_numeric($currentFilters['from'])) {
            $query->where(function ($q) use ($currentFilters) {
                $q->where('has_options', false)
                    ->where('sale_price', '>=', (float) $currentFilters['from']);
                $q->orWhereHas('variants', function ($vq) use ($currentFilters) {
                    $vq->where('status', '!=', 'draft')
                        ->where('sale_price', '>=', (float) $currentFilters['from']);
                });
            });
        }

        if (!empty($currentFilters['to']) && is_numeric($currentFilters['to'])) {
            $query->where(function ($q) use ($currentFilters) {
                $q->where('has_options', false)
                    ->where('sale_price', '<=', (float) $currentFilters['to']);
                $q->orWhereHas('variants', function ($vq) use ($currentFilters) {
                    $vq->where('status', '!=', 'draft')
                        ->where('sale_price', '<=', (float) $currentFilters['to']);
                });
            });
        }

        return $query->count();
    }

    /**
     * Get price range for products.
     *
     * @param array $products
     * @return array
     */
    private function getPriceRange(array $products): array
    {
        $minPrice = null;
        $maxPrice = null;

        foreach ($products as $product) {
            if ($product->has_options) {
                $variants = $product->relationLoaded('variants')
                    ? $product->variants->where('status', '!=', 'draft')
                    : $product->variants()->where('status', '!=', 'draft')->get();

                if ($variants->isNotEmpty()) {
                    $variantMin = (float) $variants->min('sale_price');
                    $variantMax = (float) $variants->max('sale_price');

                    $minPrice = $minPrice === null ? $variantMin : min($minPrice, $variantMin);
                    $maxPrice = $maxPrice === null ? $variantMax : max($maxPrice, $variantMax);
                }
            } else {
                $price = (float) $product->sale_price;
                $minPrice = $minPrice === null ? $price : min($minPrice, $price);
                $maxPrice = $maxPrice === null ? $price : max($maxPrice, $price);
            }
        }

        return [
            'min' => $minPrice ?? 0,
            'max' => $maxPrice ?? 0,
        ];
    }

    /**
     * Get empty filters structure.
     *
     * @return array
     */
    private function getEmptyFilters(): array
    {
        return [
            'options' => [],
            'categories' => [],
            'brands' => [],
        ];
    }
}