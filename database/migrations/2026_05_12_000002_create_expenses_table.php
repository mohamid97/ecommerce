<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['fixed', 'variable'])->default('fixed');
            $table->decimal('amount', 12, 2)->default(0);
            $table->json('data')->nullable();
            $table->timestamps();
        });

        Schema::create('expense_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_id');
            $table->string('locale')->index();
            $table->unique(['expense_id', 'locale']);
            $table->string('title');
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_translations');
        Schema::dropIfExists('expenses');
    }
};
