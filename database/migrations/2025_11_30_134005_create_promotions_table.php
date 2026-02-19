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
            $table->enum('status' , ['active' , 'inactive'])->default('active');
            $table->boolean('is_coupon')->default(false);
            $table->string('coupon_code')->nullable();
            $table->enum('type',['percent','fixed' , 'bundle' , 'bulk' ,'buy-x-get-y'])->default('percent');
            $table->decimal('type_value', 10, 2)->default(0);
            $table->enum('location',['hero','offers_section','pop_up','header_alert'])->default('hero');
            $table->enum('target',['global','category','product','brand','order'])->default('global');
            $table->string('image')->nullable();
            $table->json('categories')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('max_amount_discount', 10, 2)->nullable(); 
            $table->integer('coupon_limit')->nullable();
            $table->enum('customer_group' , ['all', 'new_user', 'registered'])->default('all');     
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
