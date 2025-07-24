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
        Schema::table('ml_site_config', function (Blueprint $table) {
            $table->integer('randuai_max_tokens')->nullable();
            $table->integer('randuai_free_tokens_daily')->nullable()->default(1000);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_site_config', function (Blueprint $table) {
            //
        });
    }
};
