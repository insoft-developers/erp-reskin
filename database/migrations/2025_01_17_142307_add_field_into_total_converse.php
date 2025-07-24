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
            $table->integer('total_product_jadi')->after('total_product');
            $table->integer('total_setengah_jadi')->after('total_product_jadi');
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
