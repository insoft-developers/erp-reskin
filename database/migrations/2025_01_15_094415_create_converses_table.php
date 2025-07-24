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
        Schema::create('ml_converses', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('reference');
            $table->integer('userid');
            $table->integer('product_type');
            $table->integer('product_id');
            $table->integer('product_quantity');
            $table->string('unit');
            $table->integer('product_price');
            $table->integer('total_price');
            $table->integer('sync_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_converses');
    }
};
