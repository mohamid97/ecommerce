<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('total_price_after_discount');
            }

            if (!Schema::hasColumn('order_items', 'product_sku')) {
                $table->string('product_sku')->nullable()->after('product_name');
            }

            if (!Schema::hasColumn('order_items', 'product_price')) {
                $table->decimal('product_price', 12, 2)->default(0)->after('product_sku');
            }

            if (!Schema::hasColumn('order_items', 'variant_combination_name')) {
                $table->string('variant_combination_name')->nullable()->after('product_price');
            }

            if (!Schema::hasColumn('order_items', 'variant_sku')) {
                $table->string('variant_sku')->nullable()->after('variant_combination_name');
            }

            if (!Schema::hasColumn('order_items', 'variant_price')) {
                $table->decimal('variant_price', 12, 2)->nullable()->after('variant_sku');
            }

            if (!Schema::hasColumn('order_items', 'variant_attributes')) {
                $table->json('variant_attributes')->nullable()->after('variant_price');
            }

            if (!Schema::hasColumn('order_items', 'product_snapshot')) {
                $table->json('product_snapshot')->nullable()->after('variant_attributes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('order_items', 'product_name') ? 'product_name' : null,
                Schema::hasColumn('order_items', 'product_sku') ? 'product_sku' : null,
                Schema::hasColumn('order_items', 'product_price') ? 'product_price' : null,
                Schema::hasColumn('order_items', 'variant_combination_name') ? 'variant_combination_name' : null,
                Schema::hasColumn('order_items', 'variant_sku') ? 'variant_sku' : null,
                Schema::hasColumn('order_items', 'variant_price') ? 'variant_price' : null,
                Schema::hasColumn('order_items', 'variant_attributes') ? 'variant_attributes' : null,
                Schema::hasColumn('order_items', 'product_snapshot') ? 'product_snapshot' : null,
            ]);

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
