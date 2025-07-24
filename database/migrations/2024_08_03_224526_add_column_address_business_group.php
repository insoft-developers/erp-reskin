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
        // province_id, city_id, district_id
        Schema::table('business_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->index()->nullable();
            $table->unsignedBigInteger('city_id')->index()->nullable();
            $table->unsignedBigInteger('district_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_groups', function (Blueprint $table) {
            $table->dropColumn(['province_id', 'city_id', 'district_id']);
        });
    }
};
