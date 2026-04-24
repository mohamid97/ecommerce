<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('points_tiers');
        Schema::dropIfExists('points_conversions');
    }

    public function down(): void
    {
        Schema::create('points_tiers', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->integer('points')->default(0);
            $table->timestamps();
        });

        Schema::create('points_conversions', function (Blueprint $table) {
            $table->id();
            $table->decimal('pound_per_point', 10, 4)->default(0.00);
            $table->timestamps();
        });
    }
};
