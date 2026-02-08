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
        Schema::create('bundels', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 10, 2);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('null');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('null');
            $table->string('bundle_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundels');
    }
};
