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
        Schema::create('option_value_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('option_value_id');
            $table->string('locale')->index();
            $table->unique(['option_value_id', 'locale']);
            $table->string('title'); // red, 5kg, 100m, etc.
            $table->foreign('option_value_id')->references('id')->on('option_values')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('option_value_translations');
    }
};