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
        Schema::table('storefronts', function (Blueprint $table) {
            $table->dropColumn('store_address');
            $table->dropColumn('payment_method');
            $table->dropColumn('shipping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storefronts', function (Blueprint $table) {
            $table->string('store_address');
            $table->json('payment_method');
            $table->json('shipping');
        });
    }
};
