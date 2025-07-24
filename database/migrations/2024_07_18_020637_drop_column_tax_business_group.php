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
        Schema::table('business_groups', function (Blueprint $table) {
            $table->dropColumn('tax');
            $table->dropColumn('tax_name');
        });

        // CREATE COLUMN TAX IN ACCOUNT
        Schema::table('ml_accounts', function (Blueprint $table) {
            $table->double('tax')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_groups', function (Blueprint $table) {
            $table->string('tax')->nullable();
            $table->string('tax_name')->nullable();
        });

        Schema::table('ml_accounts', function (Blueprint $table) {
            $table->dropColumn('tax');
        });
    }
};
