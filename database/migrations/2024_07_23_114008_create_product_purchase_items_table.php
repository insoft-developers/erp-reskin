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
        Schema::create('ml_product_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->integer('userid');
            $table->integer('purchase_id');
            $table->integer('product_id');
            $table->integer('purchase_amount');
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->integer('cost');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_product_purchase_items');
    }
};
