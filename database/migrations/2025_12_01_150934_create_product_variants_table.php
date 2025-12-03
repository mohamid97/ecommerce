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
            $table->string('sku')->unique();
            $table->string('barcode')->unique();
            $table->decimal('price', 10, 2);
            $table->enum('status', ['published', 'pending'])->default('published');
            $table->integer('stock')->default(0);
            $table->decimal('cost_price', 10, 2);
            $table->decimal('discount_value', 10, 2);
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->integer('length')->default(0);
            $table->integer('width')->default(0);
            $table->integer('height')->default(0);
            $table->integer('weight')->default(0);
            $table->integer('delivery_time')->default(0);
            $table->integer('max_time')->default(0);
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
