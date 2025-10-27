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
        Schema::create('service_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->string('locale')->index();
            $table->unique(['service_id', 'locale']);
            $table->string('title');
            $table->string('slug');
            $table->string('small_des')->nullable();
            $table->text('des')->nullable();
            $table->string('alt_image')->nullable();
            $table->string('title_image')->nullable();
            $table->string('meta_des')->nullable();
            $table->string('meta_title')->nullable();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_translations');
    }
};
