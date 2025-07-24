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
            $table->integer('randuai_tokens')->nullable()->default(0)->after('id');
            $table->integer('randuai_tokens_used')->nullable()->default(0)->after('randuai_tokens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_accounts', function (Blueprint $table) {
            //
        });
    }
};
