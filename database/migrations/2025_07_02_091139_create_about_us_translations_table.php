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
        Schema::create('about_us_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aboutus_id');
            $table->string('locale')->index();
            $table->unique(['aboutus_id', 'locale']);
            $table->string('title');
            $table->string('alt_image')->nullable();
            $table->string('title_image')->nullable();
            $table->text('des')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_des')->nullable();
            $table->text('mission')->nullable();
            $table->text('vission')->nullable();
            $table->text('services')->nullable();
            $table->text('brief')->nullable();
            $table->string('small_des')->nullable();
            $table->foreign('aboutus_id')->references('id')->on('about_us')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us_translations');
    }
};