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
        Schema::create('wallet_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('note')->nullable();
            $table->integer('amount');
            $table->string('reference');
            $table->string('publisherOrderId')->nullable();
            $table->string('type');
            $table->dateTime('payment_at')->nullable();
            $table->integer('status')->comment('0=waiting, 1=pending, 2=process, 3=complete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_logs');
    }
};
