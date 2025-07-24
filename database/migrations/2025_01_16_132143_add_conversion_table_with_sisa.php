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
        Schema::table('ml_converses', function (Blueprint $table) {
            $table->integer('total_material')->after('total_price');
            $table->integer('total_product')->after('total_material');
            $table->integer('total_sisa')->after('total_product');
            $table->integer('total_sisa2')->after('total_sisa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_converses', function (Blueprint $table) {
            //
        });
    }
};
