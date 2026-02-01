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
        Schema::create('stock_movments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->decimal('quantity', 8, 2)->nullable();
            $table->decimal('cost_price', 8, 2)->nullable();
            $table->decimal('sales_price', 8, 2);
            $table->enum('type' , ['increase' , 'decrease'])->default('increase');
            $table->string('note')->nullable();
            $table->enum('status' , ['draft' , 'active','inactive','depleted'])->default('draft');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movments');
    }
};
