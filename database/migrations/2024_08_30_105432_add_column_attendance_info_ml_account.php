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
        Schema::table('ml_account_info', function (Blueprint $table) {
            $table->dropColumn('clock_in');
            $table->dropColumn('clock_out');
            $table->dropColumn('holiday');
        });

        Schema::table('ml_accounts', function (Blueprint $table) {
            $table->time('clock_in')->default('08:00:00');
            $table->time('clock_out')->default('16:00:00');
            $table->string('holiday')->default('["Minggu"]');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
