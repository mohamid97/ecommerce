<?php

namespace Tests\Feature;

use App\Models\Api\Admin\Product;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MetaFeedTest extends TestCase
{
    use WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('product_translations');
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');

        Schema::create('brands', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('product_image')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('product_translations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('locale');
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('des')->nullable();
            $table->timestamps();
        });
    }

    public function test_meta_feed_returns_only_active_in_stock_products_with_required_fields(): void
    {
        $activeProduct = Product::create([
            'status' => 'active',
            'product_image' => 'products/active.jpg',
            'sale_price' => 199.99,
            'stock' => 5,
        ]);
        $activeProduct->translateOrNew('ar')->fill([
            'title' => 'منتج نشط',
            'slug' => 'منتج-نشط',
            'des' => 'منتج رائع',
        ])->save();

        $inactiveProduct = Product::create([
            'status' => 'draft',
            'product_image' => 'products/inactive.jpg',
            'sale_price' => 99.99,
            'stock' => 2,
        ]);
        $inactiveProduct->translateOrNew('ar')->fill([
            'title' => 'منتج غير نشط',
            'slug' => 'منتج-غير-نشط',
            'des' => 'يجب ألا يظهر',
        ])->save();

        $outOfStockProduct = Product::create([
            'status' => 'active',
            'product_image' => 'products/out.jpg',
            'sale_price' => 50.00,
            'stock' => 0,
        ]);
        $outOfStockProduct->translateOrNew('ar')->fill([
            'title' => 'منتج غير متوفر',
            'slug' => 'منتج-غير-متوفر',
            'des' => 'يجب ألا يظهر أيضا',
        ])->save();

        $noImageProduct = Product::create([
            'status' => 'active',
            'sale_price' => 30.00,
            'stock' => 1,
        ]);
        $noImageProduct->translateOrNew('ar')->fill([
            'title' => 'منتج بدون صورة',
            'slug' => 'منتج-بدون-صورة',
            'des' => 'يجب ألا يظهر',
        ])->save();

        $response = $this->get('/meta-feed.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $response->assertSee('<g:id>'.$activeProduct->id.'</g:id>', false);
        $response->assertSee('<title>منتج نشط</title>', false);
        $response->assertDontSee('منتج غير نشط', false);
        $response->assertDontSee('منتج غير متوفر', false);
        $response->assertDontSee('منتج بدون صورة', false);
    }
}
