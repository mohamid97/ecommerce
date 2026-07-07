<?php

namespace Tests\Unit\Services\Ecommerce\Cart;

use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\BundelDetails;
use App\Services\Ecommerce\Cart\CartAction;
use Tests\TestCase;

class CartActionBundleSelectionTest extends TestCase
{
    public function test_it_assigns_repeated_bundle_selections_to_distinct_detail_rows(): void
    {
        $action = new CartAction;

        $bundle = new Bundel;
        $bundle->id = 1;

        $firstDetail = new BundelDetails;
        $firstDetail->forceFill([
            'id' => 1,
            'bundel_id' => 1,
            'product_id' => 10,
            'variant_ids' => [11],
            'quantity' => 2,
        ]);

        $secondDetail = new BundelDetails;
        $secondDetail->forceFill([
            'id' => 2,
            'bundel_id' => 1,
            'product_id' => 10,
            'variant_ids' => [11],
            'quantity' => 1,
        ]);

        $bundle->setRelation('bundelDetails', collect([$firstDetail, $secondDetail]));
        $action->bundel = $bundle;

        $firstSelection = $action->findBundleDetailForSelection(10, 11, []);
        $this->assertNotNull($firstSelection);
        $this->assertSame(1, $firstSelection->id);

        $secondSelection = $action->findBundleDetailForSelection(10, 11, [$firstSelection->id]);
        $this->assertNotNull($secondSelection);
        $this->assertSame(2, $secondSelection->id);
    }
}
