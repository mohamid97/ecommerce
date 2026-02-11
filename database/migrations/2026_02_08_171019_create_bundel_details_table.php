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
        Schema::create('bundel_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('bundel_id');
            $table->unsignedBigInteger('product_id');

            $table->foreign('bundel_id')
                ->references('id')
                ->on('bundels')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->json('variant_ids')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundel_details');
    }
};
