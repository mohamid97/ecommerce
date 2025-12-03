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
        Schema::create('promotion_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotion_id');
            $table->string('locale')->index();
            $table->unique(['promotion_id', 'locale']);
            $table->string('title');
            $table->text('des');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_translations');
    }
    

};
