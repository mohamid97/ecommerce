<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = 'last_pieces';

        if (! Schema::hasColumn($table, 'variant_id')) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            });
        }
    }

    public function down(): void
    {
        $table = 'last_pieces';

        if (Schema::hasColumn($table, 'variant_id')) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('variant_id');
            });
        }
    }
};
