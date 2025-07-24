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
            $table->double('max_net_profit', 11, 2)->nullable();
            $table->double('max_gross_profit', 11, 2)->nullable();
            $table->double('max_summary', 11, 2)->nullable();
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
