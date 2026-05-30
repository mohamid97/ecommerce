<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Using a raw statement is the most reliable way to modify
        // an ENUM column in MySQL without requiring doctrine/dbal.
        \DB::statement("
            ALTER TABLE promotions
            MODIFY COLUMN location
                ENUM('hero','offers_section','pop_up','header_alert')
                NULL
                DEFAULT NULL
        ");
    }

    public function down(): void
    {
        \DB::statement("
            ALTER TABLE promotions
            MODIFY COLUMN location
                ENUM('hero','offers_section','pop_up','header_alert')
                NOT NULL
                DEFAULT 'hero'
        ");
    }
};
