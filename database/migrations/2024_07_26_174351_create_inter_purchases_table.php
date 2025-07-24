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
        Schema::create('ml_inter_purchases', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->integer('userid');
            $table->integer('product_id');
            $table->string('account_id');
            $table->integer('quantity');
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
        Schema::dropIfExists('ml_inter_purchases');
    }
};
