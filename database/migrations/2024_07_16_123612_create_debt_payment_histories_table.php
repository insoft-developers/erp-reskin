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
        Schema::create('debt_payment_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('debt_id')->index();
            $table->unsignedBigInteger('payment_to_id')->index();
            $table->unsignedBigInteger('payment_from_id')->index();
            $table->integer('amount')->default(0);
            $table->integer('balance')->default(0);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_payment_histories');
    }
};
