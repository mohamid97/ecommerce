<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->decimal('total_after_discount', 12, 2)->default(0);
            $table->decimal('total_before_discount', 12, 2)->default(0);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->integer('points_used')->nullable();
            $table->decimal('points_amount', 12, 2)->nullable();
            $table->foreignId('shipment_zone_id')->nullable()->constrained('shipment_zones')->nullOnDelete();
            $table->foreignId('shipment_city_id')->nullable()->constrained('shipment_cities')->nullOnDelete();
            $table->text('shipment_address')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->unsignedBigInteger('bundel_id')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->decimal('price_after_discount', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->decimal('total_price_after_discount', 12, 2)->default(0);
            $table->decimal('unit_cost_price', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('order_item_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('stock_movment_id')->nullable()->constrained('stock_movments')->nullOnDelete();
            $table->integer('quantity')->default(0);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_batches');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
