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
        Schema::create('contact_us_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contatcus_id');
             $table->string('locale')->index();
            $table->unique(['contatcus_id', 'locale']);
            $table->string('title');
            $table->text('des')->nullable();
            $table->string('meta_des')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('title_image')->nullable();
            $table->string('alt_image')->nullable();
            $table->foreign('contatcus_id')->references('id')->on('contact_us')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_us_translations');
    }
};