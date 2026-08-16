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
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'shipment_way_id')) {
                $table->dropConstrainedForeignId('shipment_way_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'shipment_way_id')) {
                $table->foreignId('shipment_way_id')->nullable()->after('parent_id')->constrained('shipment_ways')->nullOnDelete();
            }
        });
    }
};