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
        Schema::table('attendances', function (Blueprint $table) {
            $table->datetime('start_overtime')->nullable();
            $table->datetime('end_overtime')->nullable();
            $table->string('location_start_overtime')->nullable();
            $table->string('attachment_start_overtime')->nullable();
            $table->string('location_end_overtime')->nullable();
            $table->string('attachment_end_overtime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
