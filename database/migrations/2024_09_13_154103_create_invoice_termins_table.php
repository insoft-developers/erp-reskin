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
        Schema::create('invoice_termins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->integer('number')->default(1);
            $table->integer('status')->default(0);
            $table->date('date')->default(now());
            $table->string('payment_method')->default('kas');
            $table->string('nominal_type')->default('nominal');
            $table->string('flip_ref')->nullable();
            $table->date('payment_start_at')->nullable();
            $table->integer('nominal')->default(0);
            $table->string('payment_url')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_termins');
    }
};
