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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->integer('quantity');
            $table->integer('total_value');
            $table->integer('physical_quantity')->nullable();
            $table->integer('physical_total_value')->nullable();
            $table->integer('selisih_quantity')->nullable();
            $table->integer('selisih_total_value')->nullable();
            $table->integer('total_adjust_quantity')->nullable();
            $table->integer('total_adjust_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
