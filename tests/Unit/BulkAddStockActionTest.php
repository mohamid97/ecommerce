<?php

namespace Tests\Unit;

use App\DTO\Ecommerce\Product\BulkAddStockDTO;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\StockMovment;
use App\Services\Admin\Ecommerce\Product\Actions\Stock\BulkStoreStockAction;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BulkAddStockActionTest extends TestCase
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
            $table->boolean('has_options')->default(false);
            $table->decimal('stock', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->decimal('stock', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_movments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2);
            $table->string('note')->nullable();
            $table->enum('status', ['draft', 'active', 'inactive', 'depleted'])->default('draft');
            $table->timestamps();
        });
    }

    public function test_it_adds_stock_to_all_product_variants(): void
    {
        $product = Product::create(['sale_price' => 100, 'has_options' => true]);

        $variantOne = ProductVariant::create(['product_id' => $product->id, 'sale_price' => 100]);
        $variantTwo = ProductVariant::create(['product_id' => $product->id, 'sale_price' => 200]);

        $dto = new BulkAddStockDTO(
            $product->id,
            [$variantOne->id, $variantTwo->id],
            5.0,
            'Bulk stock update',
            50.0,
            120.0,
            'active',
        );

        $action = new BulkStoreStockAction();
        $createdStocks = $action->addStocks($dto);

        $this->assertCount(2, $createdStocks);
        $this->assertEquals(2, StockMovment::count());
        $this->assertEquals(5, $variantOne->fresh()->stock);
        $this->assertEquals(5, $variantTwo->fresh()->stock);
        $this->assertEquals(120.0, $createdStocks[0]->sale_price);
        $this->assertEquals(50.0, $createdStocks[0]->cost_price);
    }

    public function test_it_adds_stock_to_simple_product_without_variants(): void
    {
        $product = Product::create(['sale_price' => 100, 'has_options' => false]);

        $dto = new BulkAddStockDTO(
            $product->id,
            null,
            10.0,
            'Simple product stock',
            80.0,
            150.0,
            'active',
        );

        $action = new BulkStoreStockAction();
        $createdStocks = $action->addStocks($dto);

        $this->assertCount(1, $createdStocks);
        $this->assertEquals(1, StockMovment::count());
        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertEquals(150.0, $createdStocks[0]->sale_price);
        $this->assertEquals(80.0, $createdStocks[0]->cost_price);
    }
}
