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
        Schema::create('ourworks', function (Blueprint $table) {
            $table->id();
            $table->string('ourwork_image')->nullable();
            $table->string('breadcrumb')->nullable();
            $table->string('link')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->date('date')->nullable();
            $table->enum('type' , ['ourworks' , 'ourprojects'])->default('ourworks');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ourworks');
    }
};