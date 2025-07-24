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
        Schema::table('shrinkages', function (Blueprint $table) {
            $table->string('buying_with_account')->after('name')->nullable();
            $table->integer('buying_type')->after('buying_with_account')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shrinkages', function (Blueprint $table) {
            //
        });
    }
};
