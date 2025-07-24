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
        Schema::create('penjualan_product_varians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penjualan_product_id')->index();
            $table->unsignedBigInteger('varian_id')->index();
            $table->string('quantity')->default(0);
            $table->string('price')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_product_varians');
    }
};
