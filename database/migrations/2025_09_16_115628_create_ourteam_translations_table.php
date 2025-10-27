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
        Schema::create('ourteam_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ourteam_id');
            $table->string('locale')->index();
             $table->unique(['ourteam_id', 'locale']);
            $table->string('position')->nullable();
            $table->string('name');
            $table->string('experience')->nullable();
            $table->text('des')->nullable();
            $table->foreign('ourteam_id')->references('id')->on('ourteams')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ourteam_translations');
    }
};