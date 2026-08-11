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
        // Category: add default shipment way
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'shipment_way_id')) {
                $table->foreignId('shipment_way_id')->nullable()->after('parent_id')->constrained('shipment_ways')->nullOnDelete();
            }
        });

        // Product shipment: add units + optional way override
        Schema::table('product_shipements', function (Blueprint $table) {
            if (!Schema::hasColumn('product_shipements', 'units')) {
                $table->unsignedInteger('units')->default(1)->after('variant_id');
            }
            if (!Schema::hasColumn('product_shipements', 'shipment_way_id')) {
                $table->foreignId('shipment_way_id')->nullable()->after('units')->constrained('shipment_ways')->nullOnDelete();
            }
        });

        // Variant: add optional units override
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'units')) {
                $table->unsignedInteger('units')->nullable()->after('moq');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'shipment_way_id')) {
                $table->dropConstrainedForeignId('shipment_way_id');
            }
        });

        Schema::table('product_shipements', function (Blueprint $table) {
            if (Schema::hasColumn('product_shipements', 'shipment_way_id')) {
                $table->dropConstrainedForeignId('shipment_way_id');
            }
            if (Schema::hasColumn('product_shipements', 'units')) {
                $table->dropColumn('units');
            }
        });

        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'units')) {
                $table->dropColumn('units');
            }
        });
    }
};