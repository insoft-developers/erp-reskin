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
        Schema::table('ml_accounts', function (Blueprint $table) {
            $table->timestamp('upgrade_expiry')->after('is_upgraded')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_accounts', function (Blueprint $table) {
            $table->dropColumn('upgrade_expiry');
        });
    }
};
