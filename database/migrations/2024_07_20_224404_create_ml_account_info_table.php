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
        Schema::create('ml_account_info', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('ml_accounts')->onDelete('cascade');
            $table->text('store_address')->nullable();
            $table->string('province_id')->nullable();
            $table->string('province_name')->nullable();
            $table->string('city_id')->nullable();
            $table->string('city_name')->nullable();
            $table->string('subdistrict_id')->nullable();
            $table->string('subdistrict_name')->nullable();
            $table->json('payment_method')->nullable();
            $table->json('shipping')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_account_info');
    }
};
