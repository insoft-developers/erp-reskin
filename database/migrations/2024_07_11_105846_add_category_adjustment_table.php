<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('md_adjustment_products', function (Blueprint $table) {
            $table->string('category_adjustment_id')->after('product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('md_adjustment_products', function (Blueprint $table) {
            $table->dropColumn('category_adjustment_id');
        });
    }
};
