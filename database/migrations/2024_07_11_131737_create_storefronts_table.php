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
        Schema::create('storefronts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('ml_accounts')->onDelete('cascade');
            $table->text('store_address')->nullable();
            $table->json('payment_method')->nullable();
            $table->json('shipping')->nullable();
            $table->json('banner')->nullable();
            $table->string('template')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storefronts');
    }
};
