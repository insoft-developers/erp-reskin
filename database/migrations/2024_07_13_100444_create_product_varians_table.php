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
        Schema::create('md_product_varians', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->string('varian_group');
            $table->string('varian_name');
            $table->string('sku')->nullable();
            $table->integer('varian_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('md_product_varians');
    }
};
