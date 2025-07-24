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
        Schema::create('transaction_products', function (Blueprint $table) {
            $table->id();
            $table->string('references');
            $table->string('name')->nullable();
            $table->integer('phone')->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('province_id')->index();
            $table->unsignedBigInteger('city_id')->index();
            $table->unsignedBigInteger('district_id')->index();
            $table->text('address')->nullable();
            $table->string('shipping')->nullable();

            $table->integer('status_transaction')->comment('0 = Proses, 1 = Dikirim, 2 = Selesai')->default(0);
            $table->integer('status_payment')->comment('0 = Unpaid, 1 Paid')->default(0);

            $table->integer('total_qty')->default(0);
            $table->integer('sub_total')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('tax')->default(0);
            $table->integer('ongkir')->default(0);
            $table->integer('total_price')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_products');
    }
};
