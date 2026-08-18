<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE promotions MODIFY target ENUM('global', 'category', 'product', 'brand', 'bundle', 'order') NOT NULL DEFAULT 'global'");
    }

    public function down(): void
    {
        DB::table('promotions')->where('target', 'bundle')->update(['target' => 'global']);

        DB::statement("ALTER TABLE promotions MODIFY target ENUM('global', 'category', 'product', 'brand', 'order') NOT NULL DEFAULT 'global'");
    }
};
