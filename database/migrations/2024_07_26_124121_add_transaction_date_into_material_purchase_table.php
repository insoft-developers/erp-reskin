<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ml_material_purchases', function (Blueprint $table) {
            $table->date('transaction_date')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_material_purchases', function (Blueprint $table) {
            $table->date('transaction_date')->after('id');
        });
    }
};
