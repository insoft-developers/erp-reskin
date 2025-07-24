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
        Schema::create('md_customer_service_message_template_admins', function (Blueprint $table) {
            $table->id();
            $table->text('template_1')->nullable();
            $table->text('template_2')->nullable();
            $table->text('template_3')->nullable();
            $table->text('template_4')->nullable();
            $table->text('template_5')->nullable();
            $table->text('template_6')->nullable();
            $table->text('template_7')->nullable();
            $table->text('template_8')->nullable();
            $table->text('template_9')->nullable();
            $table->text('template_10')->nullable();
            $table->string('info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('md_customer_service_message_template_admins');
    }
};
