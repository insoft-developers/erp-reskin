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
        Schema::table('md_customers', function (Blueprint $table) {
            $table->string('email')->nullable();
        });

        //Schema::table('discounts', function (Blueprint $table) {
        //    $table->renameColumn('customer_id', 'max_use');
        //});

        //Schema::table('discounts', function (Blueprint $table) {
        //    $table->integer('max_use')->default(0)->change();
        //});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('md_customers', function (Blueprint $table) {
            $table->dropColumn('email');
        });

        //Schema::table('discounts', function (Blueprint $table) {
        //    $table->unsignedBigInteger('max_use')->change();
        //});

        // Rename the column back
        //Schema::table('discounts', function (Blueprint $table) {
        //    $table->renameColumn('max_use', 'customer_id');
        //});
    }
};
