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
        Schema::create('soicals', function (Blueprint $table) {
            $table->id();
            $platforms = [
                'facebook', 'twitter', 'instagram', 'youtube', 'linkedin',
                'tiktok', 'pinterest', 'snapchat', 'email', 'phone','whatsapp'
            ];

            foreach ($platforms as $platform) {
                $table->string($platform)->nullable(); 
                $table->boolean("{$platform}_cta")->default(false);
                $table->boolean("{$platform}_layout")->default(false);
            }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soicals');
    }
};