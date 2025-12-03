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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('is_active' , ['active' , 'inactive'])->default('active');
            $table->enum('is_coupon' , ['coupon' , 'no_coupon'])->default('coupon');
            $table->string('coupon_code')->nullable();
            $table->enum('type',['percent','fixed' , 'bundle' , 'bulk'])->default('percent');
            $table->decimal('type_value', 10, 2)->default(0);
            $table->enum('location',['hero','offers_section','pop_up','header_alert'])->default('hero');
            $table->enum('target',['global','category','product','brand','order'])->default('global');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->decimal('min_order_value', 10, 2)->default(0);
            $table->decimal('order_discount_value', 10, 2)->default(0);      
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
