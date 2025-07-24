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
        Schema::create('opname_items', function (Blueprint $table) {
            $table->id();
            $table->integer('opname_id');
            $table->integer('product_id');
            $table->integer('product_type');
            $table->integer('quantity');
            $table->integer('cost');
            $table->integer('total_value');
            $table->integer('physical_quantity');
            $table->integer('physical_total_value');
            $table->integer('selisih');
            $table->integer('adjust_quantity')->nullable();
            $table->string('adjust_mode')->nullable();
            $table->integer('quantity_after_adjust')->nullable();
            $table->integer('total_value_after_adjust');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opname_items');
    }
};
