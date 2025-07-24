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
            $table->integer('checkout_whatsapp')->after('delivery')->nullable();
            $table->string('template_order_info')->after('checkout_whatsapp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storefronts', function (Blueprint $table) {
            $table->integer('checkout_whatsapp')->after('delivery')->nullable();
            $table->string('template_order_info')->after('checkout_whatsapp')->nullable();
        });
    }
};
