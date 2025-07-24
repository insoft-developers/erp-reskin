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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->integer('userid');
            $table->string('material_name');
            $table->string('sku')->nullable();
            $table->integer('category_id');
            $table->string('description')->nullable();
            $table->integer('supplier_id');
            $table->integer('stock');
            $table->string('unit');
            $table->integer('min_stock');
            $table->integer('ideal_stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
