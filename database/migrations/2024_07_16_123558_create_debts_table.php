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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('debt_from')->index();
            $table->unsignedBigInteger('save_to')->index();
            $table->string('name');
            $table->enum('type', ['Utang Jangka Pendek', 'Utang Jangka Panjang']);
            $table->string('sub_type');
            $table->integer('amount')->default(0);
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
        Schema::dropIfExists('debts');
    }
};
