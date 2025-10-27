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
        Schema::create('product_option_values_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('p_o_value_id');
            $table->string('locale')->index();
            $table->unique(['p_o_value_id', 'locale']);
            $table->string('value'); // red, 5kg, 100m, etc.
            $table->foreign('p_o_value_id')->references('id')->on('product_option_values')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option_values_translations');
    }
};