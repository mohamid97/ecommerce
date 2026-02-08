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
        Schema::create('bundel_translations', function (Blueprint $table) {
           $table->id();
            $table->unsignedBigInteger('bundel_id');
            $table->string('locale')->index();
            $table->unique(['bundel_id', 'locale']);
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('des')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_des')->nullable();
            $table->foreign('bundel_id')->references('id')->on('bundels')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundel_translations');
    }
};
