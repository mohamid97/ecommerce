<?php

namespace Tests\Feature;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\Promotion;
use App\Models\Api\Ecommerce\Bundel;
use App\Services\Ecommerce\Product\PromotionPriceService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PromotionPriceServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('promotion_categories');
        Schema::dropIfExists('promotion_brands');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('bundels');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brands');

        Schema::create('brands', fn (Blueprint $table) => $table->id());
        Schema::create('categories', fn (Blueprint $table) => $table->id());
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->decimal('sale_price', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->string('discount_type')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps();
        });
        Schema::create('product_variants', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->decimal('sale_price', 10, 2);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->string('discount_type')->nullable();
            $table->timestamps();
        });
        Schema::create('bundels', function (Blueprint $table): void {
            $table->id();
            $table->decimal('discount', 10, 2)->nullable();
            $table->string('discount_type')->nullable();
            $table->timestamps();
        });
        Schema::create('promotions', function (Blueprint $table): void {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status');
            $table->boolean('is_coupon');
            $table->string('type');
            $table->string('target');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('bundel_id')->nullable();
            $table->decimal('discount', 10, 2);
            $table->decimal('max_amount_discount', 10, 2)->nullable();
            $table->timestamps();
        });
        Schema::create('promotion_brands', function (Blueprint $table): void {
            $table->unsignedBigInteger('promotion_id');
            $table->unsignedBigInteger('brand_id');
        });
        Schema::create('promotion_categories', function (Blueprint $table): void {
            $table->unsignedBigInteger('promotion_id');
            $table->unsignedBigInteger('category_id');
        });
    }

    public function test_best_automatic_promotion_or_own_discount_wins(): void
    {
        $product = Product::create([
            'sale_price' => 100,
            'discount' => 10,
            'discount_type' => 'fixed',
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sale_price' => 100,
            'discount_value' => 25,
            'discount_type' => 'fixed',
        ]);

        Promotion::create([
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
            'status' => 'active',
            'is_coupon' => false,
            'type' => 'percent',
            'target' => 'global',
            'discount' => 15,
            'max_amount_discount' => 12,
        ]);

        $this->assertSame(88.0, $product->getDiscountPrice());
        $this->assertSame(75.0, $variant->getDiscountPrice());

        Promotion::create([
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
            'status' => 'active',
            'is_coupon' => false,
            'type' => 'fixed',
            'target' => 'global',
            'discount' => 20,
        ]);

        Promotion::create([
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
            'status' => 'active',
            'is_coupon' => true,
            'type' => 'fixed',
            'target' => 'global',
            'discount' => 100,
        ]);

        $this->assertSame(80.0, $product->fresh()->getDiscountPrice());
        $this->assertSame(75.0, $variant->fresh()->getDiscountPrice());
    }

    public function test_bundle_promotion_competes_with_the_bundle_discount(): void
    {
        $bundle = Bundel::create([
            'discount' => 15,
            'discount_type' => 'percentage',
        ]);

        Promotion::create([
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
            'status' => 'active',
            'is_coupon' => false,
            'type' => 'fixed',
            'target' => 'bundle',
            'bundel_id' => $bundle->id,
            'discount' => 20,
        ]);

        $price = app(PromotionPriceService::class)->getBundleDiscountPrice(
            $bundle,
            100,
            90
        );

        $this->assertSame(80.0, $price);
    }
}
