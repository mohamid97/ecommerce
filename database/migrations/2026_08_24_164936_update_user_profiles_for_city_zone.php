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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->nullable()->after('government_id');
            $table->unsignedBigInteger('zone_id')->nullable()->after('city_id');

            $table->foreign('city_id')
                ->references('id')->on('shipment_cities')
                ->nullOnDelete();

            $table->foreign('zone_id')
                ->references('id')->on('shipment_zones')
                ->nullOnDelete();

            $table->dropColumn('government_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('government_id')->nullable()->after('user_id');

            $table->dropForeign(['zone_id']);
            $table->dropForeign(['city_id']);

            $table->dropColumn(['city_id', 'zone_id']);
        });
    }
};
