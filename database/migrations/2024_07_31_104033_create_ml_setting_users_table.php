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
        Schema::create('ml_setting_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            // SETTING PRINTER
            $table->string('printer_connection')->default('bluetooth')->comment('bluetooth, usb')->nullable();
            $table->string('printer_paper_size')->default(5.8)->comment('5.8mm, 8.0mm')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_setting_users');
    }
};
