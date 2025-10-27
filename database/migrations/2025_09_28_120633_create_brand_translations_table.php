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
        Schema::create('brand_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id');
            $table->string('locale')->index();
            $table->unique(['brand_id', 'locale']);
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('des')->nullable();
            $table->string("meta_title")->nullable();
            $table->string('meta_des')->nullable();
            $table->string('alt_image')->nullable();
            $table->string('title_image')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_translations');
    }
};