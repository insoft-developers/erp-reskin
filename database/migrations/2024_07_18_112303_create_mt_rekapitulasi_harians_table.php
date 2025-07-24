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
        Schema::create('mt_rekapitulasi_harians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('brach_id')->index();
            $table->unsignedBigInteger('mt_kas_kecil_id')->index();
            
            $table->integer('initial_cash')->default(0);
            $table->integer('cash_sale')->default(0);
            $table->integer('transfer_sales')->default(0);
            $table->integer('payment gateway sales')->default(0);
            $table->integer('cod_sales')->default(0);
            $table->integer('marketplace sales')->default(0);
            $table->integer('outlet output')->default(0);
            $table->integer('total_cash')->default(0);
            $table->integer('total_sales')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mt_rekapitulasi_harians');
    }
};
