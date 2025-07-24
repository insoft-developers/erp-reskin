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
        Schema::table('feature_request', function (Blueprint $table) {
            $table->tinyInteger('status')->comment('0 dipertimbangkan, 1 antrian, 2 sudah tersedia')->after('detail')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feature_request', function (Blueprint $table) {
            //
        });
    }
};
