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
        Schema::create('shrinkage_simulates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shrinkage_id')->index();
            $table->string('month');
            $table->integer('initial_book_value');
            $table->integer('shrinkage');
            $table->integer('final_book_value');
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shrinkage_simulates');
    }
};
