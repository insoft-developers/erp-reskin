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
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receivable_from')->index();
            $table->unsignedBigInteger('save_to')->index();
            $table->string('name');
            $table->enum('type', ['Piutang Jangka Pendek', 'Piutang Jangka Panjang']);
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
        Schema::dropIfExists('receivables');
    }
};
