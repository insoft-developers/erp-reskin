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
        Schema::table('md_products', function (Blueprint $table) {
            $table->integer('store_displayed')->nullable()->after('price_cus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('md_products', function (Blueprint $table) {
            $table->integer('store_displayed')->nullable();
        });
    }
};
