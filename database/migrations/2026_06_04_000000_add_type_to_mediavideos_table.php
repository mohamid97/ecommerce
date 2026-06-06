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
        Schema::table('mediavideos', function (Blueprint $table) {
            if (!Schema::hasColumn('mediavideos', 'type')) {
                $table->enum('type', ['train', 'consult'])->default('train')->after('link');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mediavideos', function (Blueprint $table) {
            if (Schema::hasColumn('mediavideos', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
