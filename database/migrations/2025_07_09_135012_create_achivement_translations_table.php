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
        Schema::create('achivement_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('achivement_id');
            $table->string('locale')->index();
            $table->unique(['achivement_id', 'locale']);
            $table->string('title');
            $table->text('des')->nullable();
            $table->string('meta_des')->nullable();
            $table->string('meta_title')->nullable();
            $table->foreign('achivement_id')->references('id')->on('achivements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achivement_translations');
    }
};