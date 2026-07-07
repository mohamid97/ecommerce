<?php

namespace Tests\Unit;

use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\BundelDetails;
use App\Services\Ecommerce\Cart\CartAction;
use Tests\TestCase;

class CartActionBundleSelectionTest extends TestCase
{
    public function test_it_matches_duplicate_bundle_details_by_variant_id(): void
    {
        $action = new CartAction();

        $bundle = new Bundel();
        $detailRed = new BundelDetails([
            'product_id' => 10,
            'variant_ids' => [1],
        ]);
        $detailBlue = new BundelDetails([
            'product_id' => 10,
            'variant_ids' => [2],
        ]);

        $bundle->setRelation('bundelDetails', collect([$detailRed, $detailBlue]));
        $action->bundel = $bundle;

        $matched = $action->findBundleDetailForSelection(10, 2);

        $this->assertSame($detailBlue, $matched);
    }

    public function test_it_matches_duplicate_bundle_details_by_bundle_item_id(): void
    {
        $action = new CartAction();

        $bundle = new Bundel();
        $detailFirst = new BundelDetails([
            'product_id' => 10,
            'variant_ids' => [1, 2],
        ]);
        $detailFirst->id = 21;

        $detailSecond = new BundelDetails([
            'product_id' => 10,
            'variant_ids' => [1, 2],
        ]);
        $detailSecond->id = 22;

        $bundle->setRelation('bundelDetails', collect([$detailFirst, $detailSecond]));
        $action->bundel = $bundle;

        $matched = $action->findBundleDetailForSelection(10, 1, 22);

        $this->assertSame($detailSecond, $matched);
    }
}
