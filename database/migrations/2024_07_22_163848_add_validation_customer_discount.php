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
        Schema::table('discounts', function (Blueprint $table) {
            $table->boolean('allowed_multiple_use')->default(true);
        });

        Schema::table('log_discount_uses', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn('allowed_multiple_use');
        });

        Schema::table('log_discount_uses', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });
    }
};
