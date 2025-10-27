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
        Schema::create('ourwork_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ourwork_id');
            $table->string('locale')->index();
            $table->unique(['ourwork_id', 'locale']);
            $table->string('title');
            $table->text('des')->nullable();
            $table->string('meta_des')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('slug')->nullable();
            $table->string('small_des')->nullable();
            $table->string('location')->nullable();
            $table->foreign('ourwork_id')->references('id')->on('ourworks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ourwork_translations');
    }
};