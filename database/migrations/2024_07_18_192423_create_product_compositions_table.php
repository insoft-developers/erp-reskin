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
        Schema::create('md_product_compositions', function (Blueprint $table) {
            $table->id();
            $table->integer('material_id');
            $table->integer('product_id');
            $table->string('unit');
            $table->integer('quantity');
            $table->integer('product_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('md_product_compositions');
    }
};
