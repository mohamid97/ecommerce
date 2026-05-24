<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_faq', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_id');
            $table->unsignedBigInteger('faq_id');
            $table->timestamps();

            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
            $table->foreign('faq_id')->references('id')->on('faqs')->onDelete('cascade');

            $table->unique(['blog_id', 'faq_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_faq');
    }
};