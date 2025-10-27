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
        Schema::create('mediavideo_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mediavideo_id');
            $table->string('locale')->index();
            $table->unique(['mediavideo_id', 'locale']);
            $table->string('title')->nullable();
            $table->text('des')->nullable();
            $table->foreign('mediavideo_id')->references('id')->on('mediavideos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mediavideo_translations');
    }
};