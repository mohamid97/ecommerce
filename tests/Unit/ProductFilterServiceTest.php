<?php

namespace Tests\Unit;

use App\Services\Ecommerce\Product\ProductFilterService;
use PHPUnit\Framework\TestCase;

class ProductFilterServiceTest extends TestCase
{
    public function test_buildCategoryNode_keeps_branch_when_parent_has_no_direct_products_but_has_children(): void
    {
        $service = new class extends ProductFilterService
        {
            public function publicBuildCategoryNode($category, array $productIds, array $currentFilters): ?array
            {
                $node = $this->buildCategoryNode($category, $productIds, $currentFilters);
                if ($node !== null) {
                    $this->attachCategoryChildren($node, $category->id, new \Illuminate\Support\Collection(), $productIds, $currentFilters);
                }

                return $node;
            }

            protected function getProductCountByCategory(int $categoryId, array $productIds, array $currentFilters): int
            {
                return $categoryId === 1 ? 0 : 1;
            }

            protected function attachCategoryChildren(array &$parentNode, int $parentId, $categoryMap, array $productIds, array $currentFilters): void
            {
                if ($parentId === 1) {
                    $parentNode['children'] = [
                        [
                            'id' => 'child-category',
                            'value' => 'Child Category',
                            'count' => 1,
                        ],
                    ];
                }
            }

            private function getLocalizedValue($model, string $attribute = 'title'): string
            {
                return $model->{$attribute};
            }

            private function getCategoryTranslatedName(): string
            {
                return 'Category';
            }
        };

        $category = new class
        {
            public $id = 1;
            public $slug = 'fashion';
            public $title = 'Fashion';

            public function translate($locale)
            {
                return null;
            }
        };

        $node = $service->publicBuildCategoryNode($category, [], []);

        $this->assertNotNull($node);
        $this->assertSame('fashion', $node['id']);
        $this->assertSame('Fashion', $node['value']);
        $this->assertArrayHasKey('children', $node);
        $this->assertSame('child-category', $node['children'][0]['id']);
    }
}
