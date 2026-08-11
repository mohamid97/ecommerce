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
        Schema::create('shipment_ways', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'draft', 'unavailable'])->default('active');
            $table->integer('capacity')->default(1);
            $table->timestamps();
        });

        Schema::create('shipment_way_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('way_id');
            $table->string('locale')->index();
            $table->unique(['way_id', 'locale']);
            $table->string('title');
            $table->string('des')->nullable();
            $table->foreign('way_id')->references('id')->on('shipment_ways')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_way_translations');
        Schema::dropIfExists('shipment_ways');
    }
};