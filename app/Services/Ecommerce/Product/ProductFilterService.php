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
use Illuminate\Support\Str;

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
     * Apply base filters to the query — now reading from query params.
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

        // Category filter: ?category=slug
        if (!empty($data['category'])) {
            $query->whereHas('category.translations', function ($q) use ($data) {
                $q->where('slug', $data['category']);
            });
        }

        // Brand filter: ?brand=slug
        if (!empty($data['brand'])) {
            $query->whereHas('brand.translations', function ($q) use ($data) {
                $q->where('slug', $data['brand']);
            });
        }

        // Price range filter: ?from=10&to=100
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
                $q->where(function ($q2) use ($min, $max) {
                    $q2->where('has_options', false);
                    if ($min !== null) {
                        $q2->where('sale_price', '>=', $min);
                    }
                    if ($max !== null) {
                        $q2->where('sale_price', '<=', $max);
                    }
                });
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

        // Dynamic option filters from query params: ?optionCode=value
        // Any key in data that matches an option code is treated as an option filter
        $options = Option::with('values.translations')->get();
        foreach ($options as $option) {
            $optionCode = $option->code ?: 'option_' . $option->id;
            if (!empty($data[$optionCode])) {
                $filterValues = (array) $data[$optionCode];
                // Find matching option value IDs by translated title in current locale
                $matchingValueIds = [];
                foreach ($option->values as $optValue) {
                    if (in_array($optValue->title, $filterValues)) {
                        $matchingValueIds[] = $optValue->id;
                    }
                }
                if (!empty($matchingValueIds)) {
                    $query->whereHas('variants', function ($q) use ($matchingValueIds) {
                        $q->where('status', '!=', 'draft');
                        $q->whereHas('variants', function ($vq) use ($matchingValueIds) {
                            $vq->whereIn('option_value_id', $matchingValueIds);
                        });
                    });
                }
            }
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
     * Get the translated value for the current locale.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $attribute
     * @return string
     */
    private function getLocalizedValue($model, string $attribute = 'title'): string
    {
        $locale = app()->getLocale();
        $translated = optional($model->translate($locale))->{$attribute};
        return $translated ?: $model->{$attribute};
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

        // Get all option values for these products (with translations)
        $optionValues = OptionValue::with(['option', 'option.translations', 'translations'])
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
            $firstValue = $values->first();
            if (!$firstValue || !$firstValue->option) {
                continue;
            }
            $option = $firstValue->option;

            $optionCode = $option->code ?: 'option_' . $option->id;

            $filterValues = $values->map(function ($value) use ($productIds, $currentFilters, $option, $optionCode) {
                $count = $this->getProductCountByOptionValue($value->id, $productIds, $currentFilters);

                // Get translated title for current locale for URL-friendly value
                // In URL: ?size=small (English) or ?size=صغير (Arabic)
                $currentLocale = app()->getLocale();
                $localizedValue = optional($value->translate($currentLocale))->title ?: $value->title;

                $optionData = [
                    'id' => $this->getLocalizedValue($value),
                    'value' => $this->getLocalizedValue($value),
                    'count' => $count,
                ];

                // If value_type is not text, get the value from option_value table
                if ($option->value_type !== 'text' && !empty($value->value)) {
                    $optionData['value'] = $value->value;
                }

                return $optionData;
            })->filter(function ($item) {
                return $item['count'] > 0;
            })->values();

            if ($filterValues->isNotEmpty()) {
                $filterItem = [
                    'id' => $optionCode,
                    'name' => $this->getLocalizedValue($option),
                    'type' => 'option',
                    'valueType' => $option->value_type,
                    'options' => $filterValues,
                ];

                // Add option_image if value_type is image
                if ($option->value_type === 'image' && !empty($option->option_image)) {
                    $filterItem['image'] = $option->option_image;
                }

                $filters[] = $filterItem;
            }
        }

        // Add categories filter with hierarchical structure
        $filters = $this->getCategoryFilters($productIds, $currentFilters, $filters);

        // Add brands filter
        $filters = $this->getBrandFilters($productIds, $currentFilters, $filters);

        // Add price range filter
        $filters = $this->getPriceRangeFilter($productIds, $currentFilters, $filters);

        return $filters;
    }

    /**
     * Get category filters with product counts and hierarchical structure.
     *
     * @param array $productIds
     * @param array $currentFilters
     * @param array $filters
     * @return array
     */
    private function getCategoryFilters(array $productIds, array $currentFilters, array $filters): array
    {
        $query = Category::with('translations')->whereHas('products', function ($q) use ($productIds) {
            $q->whereIn('products.id', $productIds)
                ->where('status', 'active');
        });

        $categories = $query->get();

        $categoryOptions = $categories->map(function ($category) use ($productIds, $currentFilters) {
            $count = $this->getProductCountByCategory($category->id, $productIds, $currentFilters);

            $categoryData = [
                'id' => $this->getLocalizedValue($category, 'slug'),
                'value' => $this->getLocalizedValue($category),
                'count' => $count,
            ];

            // Load children for hierarchical structure
            $children = $this->getCategoryChildren($category->id, $productIds, $currentFilters);
            if (!empty($children)) {
                $categoryData['children'] = $children;
            }

            return $categoryData;
        })->filter(function ($item) {
            return $item['count'] > 0;
        })->values();

        if ($categoryOptions->isNotEmpty()) {
            $filters[] = [
                'id' => 'category',
                'name' => $this->getCategoryTranslatedName(),
                'type' => 'category',
                'options' => $categoryOptions,
            ];
        }

        return $filters;
    }

    /**
     * Get category translated name.
     *
     * @return array
     */
    private function getCategoryTranslatedName(): string
    {
        $locale = app()->getLocale();
        $names = [
            'ar' => 'الفئات',
            'en' => 'Category',
        ];
        return $names[$locale] ?? 'Category';
    }

    /**
     * Get child categories recursively.
     *
     * @param int $parentId
     * @param array $productIds
     * @param array $currentFilters
     * @return array
     */
    private function getCategoryChildren(int $parentId, array $productIds, array $currentFilters): array
    {
        $query = Category::with('translations')->where('parent_id', $parentId)
            ->whereHas('products', function ($q) use ($productIds) {
                $q->whereIn('products.id', $productIds)
                    ->where('status', 'active');
            });

        $children = $query->get();

        return $children->map(function ($child) use ($productIds, $currentFilters) {
            $count = $this->getProductCountByCategory($child->id, $productIds, $currentFilters);

            $childData = [
                'id' => $this->getLocalizedValue($child, 'slug'),
                'value' => $this->getLocalizedValue($child),
                'count' => $count,
            ];

            // Recursively get grandchildren
            $grandchildren = $this->getCategoryChildren($child->id, $productIds, $currentFilters);
            if (!empty($grandchildren)) {
                $childData['children'] = $grandchildren;
            }

            return $childData;
        })->filter(function ($item) {
            return $item['count'] > 0;
        })->values()->toArray();
    }

    /**
     * Get brand filters with product counts.
     *
     * @param array $productIds
     * @param array $currentFilters
     * @param array $filters
     * @return array
     */
    private function getBrandFilters(array $productIds, array $currentFilters, array $filters): array
    {
        $query = Brand::with('translations')->whereHas('products', function ($q) use ($productIds) {
            $q->whereIn('products.id', $productIds)
                ->where('status', 'active');
        });

        $brands = $query->get();

        $brandOptions = $brands->map(function ($brand) use ($productIds, $currentFilters) {
            $count = $this->getProductCountByBrand($brand->id, $productIds, $currentFilters);

            return [
                'id' => $brand->slug,
                'value' => $this->getLocalizedValue($brand),
                'count' => $count,
            ];
        })->filter(function ($item) {
            return $item['count'] > 0;
        })->values();

        if ($brandOptions->isNotEmpty()) {
            $filters[] = [
                'id' => 'brand',
                'name' => $this->getBrandTranslatedName(),
                'type' => 'option',
                'options' => $brandOptions,
            ];
        }

        return $filters;
    }

    /**
     * Get brand translated name.
     *
     * @return array
     */
    private function getBrandTranslatedName(): string
    {
        $locale = app()->getLocale();
        $names = [
            'ar' => 'العلامات التجارية',
            'en' => 'Brand',
        ];
        return $names[$locale] ?? 'Brand';
    }

    /**
     * Get price range filter.
     *
     * @param array $productIds
     * @param array $currentFilters
     * @param array $filters
     * @return array
     */
    private function getPriceRangeFilter(array $productIds, array $currentFilters, array $filters): array
    {
        $minPrice = null;
        $maxPrice = null;

        $products = Product::whereIn('id', $productIds)
            ->where('status', 'active')
            ->with(['variants' => function ($q) {
                $q->where('status', '!=', 'draft');
            }])
            ->get();

        foreach ($products as $product) {
            if ($product->has_options) {
                $variants = $product->relationLoaded('variants')
                    ? $product->variants
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

        $filters[] = [
            'id' => 'price',
            'name' => $this->getPriceRangeTranslatedName(),
            'type' => 'range',
            'min' => $minPrice ?? 0,
            'max' => $maxPrice ?? 1000,
        ];

        return $filters;
    }

    /**
     * Get price range translated name.
     *
     * @return array
     */
    private function getPriceRangeTranslatedName(): string
    {
        $locale = app()->getLocale();
        $names = [
            'ar' => 'نطاق السعر',
            'en' => 'Price Range',
        ];
        return $names[$locale] ?? 'Price Range';
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

        if (!empty($currentFilters['category'])) {
            $query->whereHas('category.translations', function ($q) use ($currentFilters) {
                $q->where('slug', $currentFilters['category']);
            });
        }

        if (!empty($currentFilters['brand'])) {
            $query->whereHas('brand.translations', function ($q) use ($currentFilters) {
                $q->where('slug', $currentFilters['brand']);
            });
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

        if (!empty($currentFilters['brand'])) {
            $query->whereHas('brand.translations', function ($q) use ($currentFilters) {
                $q->where('slug', $currentFilters['brand']);
            });
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

        if (!empty($currentFilters['category'])) {
            $query->whereHas('category.translations', function ($q) use ($currentFilters) {
                $q->where('slug', $currentFilters['category']);
            });
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
            [
                'id' => 'category',
                'name' => $this->getCategoryTranslatedName(),
                'type' => 'category',
                'options' => [],
            ],
            [
                'id' => 'brand',
                'name' => $this->getBrandTranslatedName(),
                'type' => 'option',
                'options' => [],
            ],
            [
                'id' => 'price',
                'name' => $this->getPriceRangeTranslatedName(),
                'type' => 'range',
                'min' => 0,
                'max' => 1000,
            ],
        ];
    }
}