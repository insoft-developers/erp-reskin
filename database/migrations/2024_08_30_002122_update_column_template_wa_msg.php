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
        Schema::table('md_customer_service_message_templates', function (Blueprint $table) {
            $table->dropColumn('msg_template_id');
            $table->dropColumn('template');
            $table->text('template_order_in')->after('cs_id');
            $table->text('template_struk_out')->after('template_order_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('md_customer_service_message_templates', function (Blueprint $table) {
            //
        });
    }
};
