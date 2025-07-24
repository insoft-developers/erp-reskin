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
        Schema::table('ml_material_purchases', function (Blueprint $table) {
            $table->integer('supplier_id')->nullable()->after('sync_status');
            $table->string('reference')->nullable()->after('supplier_id');
            $table->string('image')->nullable()->after('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_material_purchases', function (Blueprint $table) {
            $table->integer('supplier_id')->nullable();
            $table->string('reference')->nullable();
            $table->string('image')->nullable();
        });
    }
};
