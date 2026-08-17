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
        // Add 'bundle' to the enum
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE promotions MODIFY COLUMN target ENUM('global', 'category', 'product', 'brand', 'order', 'bundle') DEFAULT 'global'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE promotions MODIFY COLUMN target ENUM('global', 'category', 'product', 'brand', 'order') DEFAULT 'global'");
    }
};
