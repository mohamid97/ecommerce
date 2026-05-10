<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // change status to enum of allowed values
            $table->enum('status', ['pending','confirmed','processing','shipped','delivered','cancelled','refunded'])->default('pending')->change();

            // add payment_status column
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['paid','unpaid','refunded'])->default('unpaid')->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // revert payment_status
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }

            // revert status to string
            $table->string('status')->default('pending')->change();
        });
    }
};
