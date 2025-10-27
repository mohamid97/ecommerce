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
        Schema::create('achivement_galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('achivement_id');
            $table->string('image');
            $table->integer('order')->nullable();
            $table->foreign('achivement_id')->references('id')->on('achivements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achivement_galleries');
    }
};