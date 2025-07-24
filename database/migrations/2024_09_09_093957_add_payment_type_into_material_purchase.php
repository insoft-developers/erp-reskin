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
        Schema::table('ml_material_purchases', function (Blueprint $table) {
            $table->string('payment_type')->after('total_purchase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_material_purchases', function (Blueprint $table) {
            $table->string('payment_type')->after('total_purchase');
        });
    }
};
