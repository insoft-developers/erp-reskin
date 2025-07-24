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
        Schema::table('ml_journal', function (Blueprint $table) {
            $table->integer('edit_count')->nullable()->default(0)->after('color_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_journal', function (Blueprint $table) {
            $table->integer('edit_count')->nullable()->default(0);
        });
    }
};
