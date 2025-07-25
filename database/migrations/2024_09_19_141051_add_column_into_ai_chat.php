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
        Schema::table('ml_ai_chat_histories', function (Blueprint $table) {
            $table->double('amount', 11, 4)->nullable()->default(0)->after('content');
            $table->boolean('has_sync')->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_ai_chat_histories', function (Blueprint $table) {
            //
        });
    }
};
