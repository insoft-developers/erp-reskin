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
        Schema::table('ml_inter_purchases', function (Blueprint $table) {
            $table->integer('cost')->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_inter_purchases', function (Blueprint $table) {
            $table->integer('cost')->after('quantity');
        });
    }
};
