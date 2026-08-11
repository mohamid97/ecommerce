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
        Schema::create('shipment_way_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('way_id')->constrained('shipment_ways')->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained('shipment_zones')->cascadeOnDelete();
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('status', ['active', 'draft', 'unavailable'])->default('active');
            $table->unique(['way_id', 'zone_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_way_zones');
    }
};