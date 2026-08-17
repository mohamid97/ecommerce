<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['new_products', 'last_pieces'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // MySQL cannot drop a unique index that backs a foreign key.
                // Drop the FK first, swap the unique index, then restore the FK.
                $table->dropForeign([$tableName === 'new_products'
                    ? 'new_products_product_id_foreign'
                    : 'last_pieces_product_id_foreign'
                ]);
                $table->dropUnique(['product_id']);
                $table->unique(['product_id', 'variant_id']);
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        foreach (['new_products', 'last_pieces'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropUnique(['product_id', 'variant_id']);
                $table->unique('product_id');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }
    }
};
