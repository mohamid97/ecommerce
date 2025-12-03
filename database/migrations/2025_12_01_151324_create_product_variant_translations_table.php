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
        Schema::create('product_variant_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->string('locale')->index();
            $table->unique(['product_variant_id', 'locale']);
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('des')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_des')->nullable();
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_translations');
    }
};
