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
        Schema::create('ourwork_galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ourwork_id');
            $table->string('image');
            $table->integer('order')->nullable();
            $table->foreign('ourwork_id')->references('id')->on('ourworks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ourwork_galleries');
    }
};