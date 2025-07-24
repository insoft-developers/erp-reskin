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
        Schema::create('whatsapp_crm_providers', function (Blueprint $table) {
            $table->id();
            // buat kolom terhubung dengan table users
            $table->foreignId('owner_id')->constrained('ml_accounts')->onDelete('cascade')->comment('The owner of the WhatsApp CRM provider');
            $table->string('send_message_url', 100)->default('https://chat.ping.co.id/api-app/whatsapp/send-message')->comment('The base URL of the WhatsApp CRM provider');
            $table->string('send_method', 10)->default('POST')->comment('The HTTP method used to send messages to the WhatsApp CRM provider');
            $table->json('credentials')->comment('The credentials required to authenticate with the WhatsApp CRM provider');
            $table->string('provider_name', 30)->default('ping')->comment('The name of the WhatsApp CRM provider');
            $table->tinyInteger('is_active')->default(1)->comment('Indicates whether the WhatsApp CRM provider is active or not');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_crm_providers');
    }
};
