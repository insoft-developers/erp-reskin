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
        Schema::table('shrinkages', function (Blueprint $table) {
            $table->integer('quantity')->after('initial_value')->nullable();
            $table->integer('buying_price')->after('quantity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shrinkages', function (Blueprint $table) {
            //
        });
    }
};
