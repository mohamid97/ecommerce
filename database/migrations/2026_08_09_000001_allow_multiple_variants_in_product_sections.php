<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['new_products', 'last_pieces'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropUnique(['product_id']);
                $table->unique(['product_id', 'variant_id']);
            });
        }
    }

    public function down(): void
    {
        foreach (['new_products', 'last_pieces'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropUnique(['product_id', 'variant_id']);
                $table->unique('product_id');
            });
        }
    }
};
