<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sales_number')) {
                $table->unsignedBigInteger('sales_number')->default(0)->after('stock');
            }
        });

        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'sales_number')) {
                $table->unsignedBigInteger('sales_number')->default(0)->after('stock');
            }
        });

        Schema::table('bundels', function (Blueprint $table) {
            if (!Schema::hasColumn('bundels', 'sales_number')) {
                $table->unsignedBigInteger('sales_number')->default(0)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bundels', function (Blueprint $table) {
            if (Schema::hasColumn('bundels', 'sales_number')) {
                $table->dropColumn('sales_number');
            }
        });

        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'sales_number')) {
                $table->dropColumn('sales_number');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sales_number')) {
                $table->dropColumn('sales_number');
            }
        });
    }
};

