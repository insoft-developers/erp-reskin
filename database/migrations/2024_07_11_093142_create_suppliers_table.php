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
        Schema::create('md_suppliers', function (Blueprint $table) {
            $table->id();
            $table->integer('userid');
            $table->string('name');
            $table->string('contact_name');
            $table->string('phone');
            $table->string('email');
            $table->string('fax');
            $table->string('website');
            $table->string('jalan1');
            $table->string('jalan2');
            $table->string('postal_code');
            $table->string('province');
            $table->string('country');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('md_suppliers');
    }
};
