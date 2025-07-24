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
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();
            $table->integer('pelanggan_id');
            $table->integer('qrcode_id');
            $table->integer('nomor_meja');
            $table->date('tgl_reservasi');
            $table->time('jam_reservasi');
            $table->integer('jumlah')->nullable();
            $table->integer('status')->default(0);
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};
