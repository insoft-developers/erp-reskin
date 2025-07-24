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
        Schema::create('md_adjustment_inter_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('adjustment_id')->index();
            $table->unsignedBigInteger('md_inter_product_id')->index();
            $table->unsignedBigInteger('category_adjustment_id')->index();
            $table->integer('quantity')->default(0);
            $table->string('type')->default('addition');
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('md_adjustment_inter_products');
    }
};
