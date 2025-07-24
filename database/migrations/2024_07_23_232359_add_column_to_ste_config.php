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
            $table->double('min_withdraw', 11, 2)->default(0)->comment('Minimal penarikan dalam bentuk nominal rupiah');
            $table->double('min_topup', 11, 2)->default(0)->comment('Minimal topup dalam bentuk nominal rupiah');
            $table->double('fee_withdraw', 11, 2)->default(2500)->comment('biaya penarikan per transaksi');
            $table->double('fee_payment_gateway', 5, 2)->default(0.7)->comment('biaya fee payment gateway (untuk saat ini di duitku)');
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
