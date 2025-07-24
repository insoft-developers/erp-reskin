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
        Schema::table('mt_rekapitulasi_harians', function (Blueprint $table) {
            $table->dropColumn('marketplace sales');
            $table->dropColumn('outlet output');
            $table->integer('marketplace_sales')->default(0);
            $table->integer('outlet_output')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mt_rekapitulasi_harians', function (Blueprint $table) {
            //
        });
    }
};
