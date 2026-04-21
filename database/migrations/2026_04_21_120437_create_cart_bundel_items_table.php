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
        Schema::create('cart_bundel_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_item_id')->constrained('cart_items')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
            // foreging key
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('variant_id')->references('id')->on('product_variants')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_bundel_items');
    }
};
