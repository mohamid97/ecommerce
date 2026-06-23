<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('last_pieces', function (Blueprint $table) {
            // drop existing unique index on product_id
            $table->dropUnique(['product_id']);

            // add variant_id column
            $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');

            // add foreign key to product_variants
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');

            // add composite unique constraint for product_id + variant_id
            $table->unique(['product_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::table('last_pieces', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'variant_id']);
            $table->dropForeign(['variant_id']);
            $table->dropColumn('variant_id');
            // restore unique on product_id
            $table->unique('product_id');
        });
    }
};
