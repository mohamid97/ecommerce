<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('govs', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'government_id')) {
                $table->foreignId('government_id')->nullable()->after('shipment_city_id')->constrained('govs')->nullOnDelete();
            }

            if (Schema::hasColumn('orders', 'government')) {
                $table->dropColumn('government');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'government_id')) {
                $table->dropConstrainedForeignId('government_id');
            }

            if (!Schema::hasColumn('orders', 'government')) {
                $table->string('government')->nullable()->after('shipment_zone_id');
            }
        });

        Schema::dropIfExists('govs');
    }
};
