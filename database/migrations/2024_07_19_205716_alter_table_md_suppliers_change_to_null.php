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
        Schema::table('md_suppliers', function (Blueprint $table) {
            $table->string('contact_name')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('fax')->nullable()->change();
            $table->string('website')->nullable()->change();
            $table->string('jalan1')->nullable()->change();
            $table->string('jalan2')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
            $table->string('province')->nullable()->change();
            $table->string('country')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('md_suppliers', function (Blueprint $table) {
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
        });
    }
};
