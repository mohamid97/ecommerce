<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['new_products', 'last_pieces'] as $tableName) {
            // The original unique index also backs the product_id foreign key.
            // Create a non-unique replacement before removing it, otherwise MySQL
            // rejects the drop with SQLSTATE[HY000] error 1553.
            $supportingIndex = "{$tableName}_product_id_fk_index";

            Schema::table($tableName, function (Blueprint $table) use ($supportingIndex) {
                $table->index('product_id', $supportingIndex);
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropUnique(['product_id']);
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->unique(['product_id', 'variant_id']);
            });
        }
    }

    public function down(): void
    {
        foreach (['new_products', 'last_pieces'] as $tableName) {
            $supportingIndex = "{$tableName}_product_id_fk_index";

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropUnique(['product_id', 'variant_id']);
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->unique('product_id');
            });

            Schema::table($tableName, function (Blueprint $table) use ($supportingIndex) {
                $table->dropIndex($supportingIndex);
            });
        }
    }
};
