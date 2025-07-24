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
        Schema::create('ml_absensi_staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('approved_by')->index()->comment('Diapprove Oleh (ml_account)');
            $table->text('address')->nullable();
            $table->string('link_address');
            $table->string('photo');
            $table->string('visited');
            $table->longText('contact')->nullable();
            $table->integer('is_approved')->default(0);
            $table->longText('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_absensi_staff');
    }
};
