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
        Schema::table('mt_pengeluaran_outlets', function (Blueprint $table) {
            $table->integer('sync_status')->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mt_pengeluaran_outlets', function (Blueprint $table) {
            $table->integer('sync_status')->nullable();
        });
    }
};
