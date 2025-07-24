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
        Schema::table('md_customers', function (Blueprint $table) {
            $table->bigInteger('province_id')->nullable()->comment('relation from ro_provinces');
            $table->bigInteger('city_id')->nullable()->comment('relation from ro_cities');
            $table->bigInteger('district_id')->nullable()->comment('relation from ro_districts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('md_customers', function (Blueprint $table) {
            //
        });
    }
};
