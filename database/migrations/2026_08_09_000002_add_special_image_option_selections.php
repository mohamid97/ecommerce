<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gerneral_variant_galleries', function (Blueprint $table) {
            $table->json('alt_text')->nullable()->after('image');
            $table->integer('order')->nullable()->after('product_id');
        });

        Schema::create('special_image_option_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->constrained('gerneral_variant_galleries')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('options')->cascadeOnDelete();
            $table->foreignId('option_value_id')->constrained('option_values')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['image_id', 'option_value_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_image_option_selections');

        Schema::table('gerneral_variant_galleries', function (Blueprint $table) {
            $table->dropColumn(['alt_text', 'order']);
        });
        
    }
};
