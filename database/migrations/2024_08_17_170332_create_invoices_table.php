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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('invoice_from')->nullable();
            $table->unsignedBigInteger('client_id')->index();
            $table->string('invoice_number')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('signature_name')->nullable();
            $table->string('signature_position')->nullable();
            $table->date('created')->nullable();
            $table->unsignedBigInteger('currency_id')->index();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->integer('status')->default(0)->comment('0 = Unpaid, 1 = Paid');
            
            $table->integer('kurs')->default(0);
            $table->string('discount_type')->nullable();
            $table->integer('sub_total')->default(0);
            $table->integer('discount_value')->default(0);
            $table->integer('tax')->default(0);
            $table->integer('discount_amount')->default(0);
            $table->integer('tax_amount')->default(0);
            $table->integer('grand_total')->default(0);

            $table->string('flip_ref')->nullable();
            $table->string('payment_url')->nullable();
            $table->date('payment_start_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
