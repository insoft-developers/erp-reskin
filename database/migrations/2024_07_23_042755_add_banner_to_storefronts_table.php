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
            $table->string('banner_image1');
            $table->string('banner_link1');
            $table->string('banner_image2');
            $table->string('banner_link2');
            $table->string('banner_image3');
            $table->string('banner_link3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storefronts', function (Blueprint $table) {
            $table->dropColumn('banner_image1');
            $table->dropColumn('banner_link1');
            $table->dropColumn('banner_image2');
            $table->dropColumn('banner_link2');
            $table->dropColumn('banner_image3');
            $table->dropColumn('banner_link3');
        });
    }
};
