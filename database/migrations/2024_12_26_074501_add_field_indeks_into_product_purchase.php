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
        Schema::table('ml_product_purchase_items', function (Blueprint $table) {
            $table->integer('indeks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_product_purchase_items', function (Blueprint $table) {
            $table->integer('indeks')->nullable();
        });
    }
};
