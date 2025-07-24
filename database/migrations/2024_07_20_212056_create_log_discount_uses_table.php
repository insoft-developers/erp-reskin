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
        Schema::create('log_discount_uses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discount_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('penjualan_id')->index();
            $table->integer('total_amount');
            $table->integer('discount_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_discount_uses');
    }
};
