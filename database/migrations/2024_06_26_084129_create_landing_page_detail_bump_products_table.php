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
        Schema::create('landing_page_detail_bump_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('landing_page_id');
            $table->integer('product_id');
            $table->string('custom_name')->nullable();
            $table->string('custom_photo')->nullable();
            $table->double('discount', 5, 2)->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('landing_page_id')->references('id')->on('landing_pages')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('md_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropForeign(['landing_page_id']);
            $table->dropForeign(['product_id']);
            $table->dropColumn('custom_name');
            $table->dropColumn('custom_photo');
            $table->dropColumn('with_customer_email');
            $table->dropColumn('discount');
            $table->dropColumn('title');
            $table->dropColumn('html_code');
            $table->dropColumn('description');
        });

        Schema::dropIfExists('landing_page_detail_bump_products');
    }
};
