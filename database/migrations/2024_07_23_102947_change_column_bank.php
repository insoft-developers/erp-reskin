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
        Schema::table('ml_banks', function (Blueprint $table) {
            $table->dropColumn('duitku_code');
        });

        Schema::table('ml_banks', function (Blueprint $table) {
            $table->string('bank_code');
            $table->string('vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_banks', function (Blueprint $table) {
            $table->dropColumn('bank_code');
            $table->dropColumn('vendor');
        });
    }
};
