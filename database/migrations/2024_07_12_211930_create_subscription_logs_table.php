<?php

use App\Models\Account;
use App\Models\SubscriptionPlan;
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
        Schema::create('subscription_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SubscriptionPlan::class, 'subscription_id');
            $table->foreignIdFor(Account::class);
            $table->string('reference');
            $table->string('publisherOrderId')->nullable();
            $table->integer('status')->default(0);
            $table->timestamp('subscription_expiry_date')->nullable();
            $table->timestamp('payment_due_date')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_logs');
    }
};
