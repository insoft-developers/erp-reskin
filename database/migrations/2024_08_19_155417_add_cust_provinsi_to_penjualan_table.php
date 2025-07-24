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
        Schema::table('penjualan', function (Blueprint $table) {
            $table->string('cust_provinsi')->after('cust_email')->nullable();
            $table->string('cust_kota')->after('cust_provinsi')->nullable();
            $table->string('cust_kecamatan_id')->after('cust_kota')->nullable();
            $table->string('shipping_method')->after('shipping')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropColumn('cust_provinsi');
            $table->dropColumn('cust_kota');
            $table->dropColumn('cust_kecamatan_id');
            $table->dropColumn('shipping_method');
        });
    }
};
