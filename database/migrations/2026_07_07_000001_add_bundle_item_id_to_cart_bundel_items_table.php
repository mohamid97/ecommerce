<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_bundel_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_bundel_items', 'bundle_item_id')) {
                $table->foreignId('bundle_item_id')
                    ->nullable()
                    ->after('cart_item_id')
                    ->constrained('bundel_details')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_bundel_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_bundel_items', 'bundle_item_id')) {
                $table->dropConstrainedForeignId('bundle_item_id');
            }
        });
    }
};
