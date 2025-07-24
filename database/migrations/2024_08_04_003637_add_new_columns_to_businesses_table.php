<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_groups', function (Blueprint $table) {
            $table->integer('business_category')->nullable();
            $table->text('business_district')->nullable();
            $table->text('business_address')->nullable();
            $table->string('business_phone', 30)->nullable();
            $table->string('model', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_groups', function (Blueprint $table) {
            $table->dropColumn('business_category');
            $table->dropColumn('business_district');
            $table->dropColumn('business_address');
            $table->dropColumn('business_phone');
            $table->dropColumn('model');
        });
    }
};
