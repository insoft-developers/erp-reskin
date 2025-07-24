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
            $table->string('company_email')->nullable();
            $table->string('business_fields')->nullable();
            $table->string('npwp')->nullable();
            $table->double('tax')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('tax_name')->nullable();
            $table->string('bank')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_groups', function (Blueprint $table) {
            $table->dropColumn(['company_email', 'business_fields', 'npwp', 'tax', 'no_rekening', 'tax_name', 'bank']);
        });
    }
};
