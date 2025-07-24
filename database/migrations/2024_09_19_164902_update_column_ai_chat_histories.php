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
            $table->dropColumn('user_id');
            $table->dropColumn('conversation_key');
            $table->bigInteger('ml_ai_chat_id')->after('id');
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
