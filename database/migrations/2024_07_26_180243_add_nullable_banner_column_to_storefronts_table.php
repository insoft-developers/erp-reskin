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
        Schema::table('storefronts', function (Blueprint $table) {
            $table->string('banner_image1')->nullable()->change();
            $table->string('banner_link1')->nullable()->change();
            $table->string('banner_image2')->nullable()->change();
            $table->string('banner_link2')->nullable()->change();
            $table->string('banner_image3')->nullable()->change();
            $table->string('banner_link3')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storefronts', function (Blueprint $table) {
            //
        });
    }
};
