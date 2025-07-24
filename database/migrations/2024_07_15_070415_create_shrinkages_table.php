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
        Schema::create('shrinkages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ml_fixed_asset_id')->index();
            $table->unsignedBigInteger('ml_accumulated_depreciation_id')->index();
            $table->unsignedBigInteger('ml_admin_general_fee_id')->index();
            $table->string('name');
            $table->string('initial_value');
            $table->integer('useful_life')->default(0);
            $table->integer('residual_value')->default(0);
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
        Schema::dropIfExists('shrinkages');
    }
};
