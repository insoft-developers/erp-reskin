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
        Schema::table('ml_converse_items', function (Blueprint $table) {
            $table->integer('item_price')->after('quantity');
            $table->integer('item_total')->after('item_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_converse_items', function (Blueprint $table) {
            //
        });
    }
};
