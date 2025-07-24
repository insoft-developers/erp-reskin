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
            $table->unsignedBigInteger('branch_id')->after('uuid')->nullable()->index();
            $table->unsignedBigInteger('position_id')->after('branch_id')->nullable()->index();
            $table->string('phone')->after('fullname')->nullable();
            $table->date('start_date')->after('is_active')->nullable();
            $table->string('pin',6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('position_id');
            $table->dropColumn('phone');
            $table->dropColumn('start_date');
        });
    }
};
