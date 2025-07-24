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
        Schema::table('md_product_varians', function (Blueprint $table) {
            $table->tinyInteger('single_pick')->nullable()->change();
            $table->integer('max_quantity')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('md_product_varians', function (Blueprint $table) {
            //
        });
    }
};
