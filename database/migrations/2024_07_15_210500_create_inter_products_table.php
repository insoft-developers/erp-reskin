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
        Schema::create('md_inter_products', function (Blueprint $table) {
            $table->id();
            $table->integer('userid');
            $table->string('product_name');
            $table->string('sku');
            $table->integer('category_id');
            $table->integer('cost');
            $table->string('composition');
            $table->string('description')->nullable();
            $table->integer('stock');
            $table->string('unit');
            $table->integer('min_stock')->nullable();
            $table->integer('ideal_stock')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('md_inter_products');
    }
};
