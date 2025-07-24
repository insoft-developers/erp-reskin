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
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('product_id');
            $table->text('script_header')->nullable();
            $table->text('script_header_payment_page')->nullable();
            $table->text('script_header_wa_page')->nullable();
            $table->boolean('click_to_wa')->default(0);
            $table->boolean('with_customer_name')->default(1);
            $table->boolean('with_customer_wa_number')->default(1);
            $table->boolean('with_customer_email')->default(1);
            $table->boolean('with_customer_full_address')->default(1);
            $table->boolean('with_customer_proty')->default(1)->comment('Ketika menggunakan informasi provinsi dan kota pengguna');
            $table->text('html_code')->nullable();
            $table->string('text_submit_button')->default('Kirim Permintaan')->comment('Tombol aksi form atau text tombol submit form');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('ml_accounts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('md_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            $table->dropColumn('with_customer_name');
            $table->dropColumn('with_customer_wa_number');
            $table->dropColumn('with_customer_email');
            $table->dropColumn('with_customer_full_address');
            $table->dropColumn('with_customer_proty');
            $table->dropColumn('html_code');
            $table->dropColumn('text_submit_button');
        });

        Schema::dropIfExists('landing_pages');
    }
};
