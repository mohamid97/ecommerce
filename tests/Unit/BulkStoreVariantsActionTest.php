<?php

namespace Tests\Unit;

use App\DTO\Ecommerce\Product\BulkStoreVariantsDTO;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\VariantOptionValue;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\BulkStoreVariantsAction;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BulkStoreVariantsActionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ]);

        DB::purge('sqlite');

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->string('discount_type')->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->string('discount_type')->nullable();
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->integer('moq')->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->integer('delivery_time')->default(0);
            $table->integer('max_time')->default(0);
            $table->boolean('is_default')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('option_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('option_id');
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('option_id');
            $table->timestamps();
        });

        Schema::create('variant_option_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->unsignedBigInteger('option_id');
            $table->unsignedBigInteger('option_value_id');
            $table->timestamps();
        });

        Schema::create('langs', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('lang');
            $table->timestamps();
        });

        Schema::create('product_variant_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->string('locale');
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('des')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_des')->nullable();
            $table->timestamps();
        });
    }

    public function test_it_creates_multiple_variants_for_a_product(): void
    {
        $product = Product::create(['sale_price' => 100, 'status' => 'active']);

        $colorOptionId = DB::table('options')->insertGetId(['title' => 'Color', 'created_at' => now(), 'updated_at' => now()]);
        $sizeOptionId = DB::table('options')->insertGetId(['title' => 'Size', 'created_at' => now(), 'updated_at' => now()]);

        $redId = DB::table('option_values')->insertGetId(['option_id' => $colorOptionId, 'title' => 'Red', 'created_at' => now(), 'updated_at' => now()]);
        $blueId = DB::table('option_values')->insertGetId(['option_id' => $colorOptionId, 'title' => 'Blue', 'created_at' => now(), 'updated_at' => now()]);
        $smallId = DB::table('option_values')->insertGetId(['option_id' => $sizeOptionId, 'title' => 'Small', 'created_at' => now(), 'updated_at' => now()]);
        $largeId = DB::table('option_values')->insertGetId(['option_id' => $sizeOptionId, 'title' => 'Large', 'created_at' => now(), 'updated_at' => now()]);

        ProductOption::create(['product_id' => $product->id, 'option_id' => $colorOptionId]);
        ProductOption::create(['product_id' => $product->id, 'option_id' => $sizeOptionId]);

        $dto = new BulkStoreVariantsDTO($product->id, [
            'discount' => 10,
            'discount_type' => 'fixed',
            'sale_price' => 100,
            'sku' => 'SKU-1',
            'barcode' => 'BAR-1',
        ], [
            [
                'option_value_ids' => [$redId, $smallId],
            ],
            [
                'option_value_ids' => [$blueId, $largeId],
            ],
        ]);

        $action = new BulkStoreVariantsAction();
        $created = $action->store($dto);

        $this->assertCount(2, $created);
        $this->assertEquals(2, ProductVariant::count());
        $this->assertEquals(4, VariantOptionValue::count());

        $variantValues = ProductVariant::with('variants')->get()->pluck('variants')->flatten()->pluck('option_value_id')->all();
        $this->assertContains($redId, $variantValues);
        $this->assertContains($smallId, $variantValues);
        $this->assertContains($blueId, $variantValues);
        $this->assertContains($largeId, $variantValues);

        $matchingVariant = ProductVariant::with('variants')->get()->first(function ($variant) use ($redId, $smallId) {
            $optionValueIds = $variant->variants->pluck('option_value_id')->map(fn ($id) => (int) $id)->sort()->values()->all();

            return $optionValueIds === [(int) $redId, (int) $smallId];
        });

        $this->assertNotNull($matchingVariant);
        $this->assertEquals('SKU-1', $matchingVariant->sku);
        $this->assertEquals('BAR-1', $matchingVariant->barcode);
        $this->assertEquals(10, ProductVariant::first()->discount_value);
        $this->assertEquals('fixed', ProductVariant::first()->discount_type);
    }
}
