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
            $table->double('randuai_fee', 5, 3)->nullable()->default('0.018')->after('randuai_free_tokens_daily');
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
