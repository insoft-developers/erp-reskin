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
        Schema::create('alamat_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->integer('pelanggan_id');
            $table->string('kode_prov', 5);
            $table->string('nama_prov', 100);
            $table->string('kode_kab_kota', 5);
            $table->string('nama_kab_kota', 100);
            $table->string('kode_kec', 5);
            $table->string('nama_kec', 100);
            $table->string('kode_kel', 5);
            $table->string('nama_kel', 100);
            $table->text('detail_alamat')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamat_pelanggan');
    }
};
