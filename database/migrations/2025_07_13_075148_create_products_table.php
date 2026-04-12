<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_image')->nullable();
            $table->string('breadcrumb')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->tinyInteger('order')->nullable();
            $table->boolean('on_demand')->default(false);
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->enum('discount_type' , ['fixed' , 'percentage'])->nullable();
            $table->boolean('has_options')->default(false);
            $table->enum('status' , ['active', 'draft','unavailable'])->default('active');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->boolean('is_featured')->default(false);
            $table->integer('stock')->default(0)->nullable();
            $table->json('related_products')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};