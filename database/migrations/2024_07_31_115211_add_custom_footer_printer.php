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
        Schema::table('ml_setting_users', function (Blueprint $table) {
            $table->text('printer_custom_footer')->comment('custom footer text for printer paper')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_setting_users', function (Blueprint $table) {
            $table->dropColumn('printer_custom_footer');
        });
    }
};
