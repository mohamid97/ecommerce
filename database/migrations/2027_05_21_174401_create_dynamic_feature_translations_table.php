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
        Schema::create('dynamic_feature_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dynamic_feature_id');
            $table->string('locale')->index();
            $table->unique(['dynamic_feature_id', 'locale']);
            
            $table->string('title');
            $table->string('small_des')->nullable();
            $table->text('des')->nullable();
            
            $table->foreign('dynamic_feature_id', 'df_trans_df_id_foreign')
                ->references('id')->on('dynamic_features')
                ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_feature_translations');
    }
};
