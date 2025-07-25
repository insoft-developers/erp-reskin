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
            $table->dropColumn('bank');
        });

        Schema::table('business_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_groups', function (Blueprint $table) {
            $table->dropColumn('bank_id');
        });
    }
};
