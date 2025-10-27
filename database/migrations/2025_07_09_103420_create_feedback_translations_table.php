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
        Schema::create('feedbacks_translations', function (Blueprint $table) {
            $table->id();
           $table->unsignedBigInteger('feedback_id');
            $table->string('locale')->index();
            $table->unique(['feedback_id', 'locale']);
            $table->string('title');
            $table->string('small_des')->nullable();
            $table->text('des')->nullable();
            $table->string('meta_des')->nullable();
            $table->string('meta_title')->nullable();
            $table->foreign('feedback_id')->references('id')->on('feedbacks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_translations');
    }
};