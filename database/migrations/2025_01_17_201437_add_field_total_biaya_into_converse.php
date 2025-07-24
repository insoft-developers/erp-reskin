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
        Schema::table('ml_converses', function (Blueprint $table) {
            $table->integer('total_biaya')->after('total_sisa2')->nullable();
            $table->string('cost_account')->after('total_biaya')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_converses', function (Blueprint $table) {
            //
        });
    }
};
