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
        Schema::table('wallet_logs', function (Blueprint $table) {
            $table->double('amount', 11, 4)->change(); // Mengubah tipe kolom balance menjadi double
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_logs', function (Blueprint $table) {
            //
        });
    }
};
