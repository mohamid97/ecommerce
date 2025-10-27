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
        Schema::create('product_specification_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_spec_id');
            $table->string('locale')->index();
            $table->unique(['product_spec_id', 'locale']);
            $table->string('prop');
            $table->text('value');
            $table->foreign('product_spec_id')->references('id')->on('product_specifications')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_specification_translations');
    }
};
