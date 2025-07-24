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
        Schema::table('md_adjustments', function (Blueprint $table) {
            $table->unsignedBigInteger('category_adjustment_id')->index();
            $table->string('type')->default('product')->comment('product, material, inter_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
