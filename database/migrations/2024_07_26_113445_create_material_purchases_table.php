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
        Schema::create('ml_material_purchases', function (Blueprint $table) {
            $table->id();
            $table->integer('userid');
            $table->string('account_id');
            $table->integer('product_count');
            $table->integer('tax')->nullable();
            $table->integer('discount')->nullable();
            $table->integer('other_expense')->nullable();
            $table->integer('total_purchase');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_material_purchases');
    }
};
