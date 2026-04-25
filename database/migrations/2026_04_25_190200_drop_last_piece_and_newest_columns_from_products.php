<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_last_piece')) {
                $table->dropColumn('is_last_piece');
            }

            if (Schema::hasColumn('products', 'is_newest')) {
                $table->dropColumn('is_newest');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_last_piece')) {
                $table->boolean('is_last_piece')->default(false)->after('is_featured');
            }

            if (!Schema::hasColumn('products', 'is_newest')) {
                $table->boolean('is_newest')->default(false)->after('is_last_piece');
            }
        });
    }
};

