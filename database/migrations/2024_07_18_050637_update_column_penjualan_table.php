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
        Schema::table('penjualan', function (Blueprint $table) {
            $table->boolean('payment_status')->default(false)->after('status');
            $table->dateTime('payment_at')->nullable()->after('payment_status');
            $table->double('order_total', 11, 2)->nullable()->after('shipping');
            $table->integer('qr_codes_id')->nullable()->after('payment_method');
            $table->integer('branch_id')->nullable()->after('qr_codes_id');
            $table->integer('staff_id')->nullable()->after('branch_id');
            $table->boolean('sync_status')->nullable()->after('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            //
        });
    }
};
