<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('points_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->integer('points')->default(0);
            // Pound value per single point
            $table->decimal('pound_per_point', 10, 4)->default(0.00);
            $table->timestamps();
        });

        // seed a default single row
        DB::table('points_settings')->insert([
            'min_order_amount' => 0,
            'points' => 0,
            'pound_per_point' => 0.05,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('points_settings');
    }
};
