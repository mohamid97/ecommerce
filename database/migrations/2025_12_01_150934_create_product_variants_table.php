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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->enum('status', ['published', 'pending'])->default('published');
            $table->integer('stock')->default(0)->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->integer('length')->default(0)->nullable();
            $table->integer('width')->default(0)->nullable();
            $table->integer('height')->default(0)->nullable();
            $table->integer('weight')->default(0)->nullable();
            $table->integer('delivery_time')->default(0)->nullable();
            $table->integer('max_time')->default(0)->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
