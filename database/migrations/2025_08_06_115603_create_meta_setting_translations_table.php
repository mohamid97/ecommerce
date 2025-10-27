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
        Schema::create('meta_setting_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meta_setting_id');
            $table->string('locale')->index();
            $table->unique(['meta_setting_id', 'locale']);
            $table->text('meta_title')->nullable();
            $table->text('meta_des')->nullable();
            $table->foreign('meta_setting_id')->references('id')->on('meta_settings')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_setting_translations');
    }
};