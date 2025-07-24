<?php

namespace App\Jobs;

use App\Traits\WhatsappTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessageGlobal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WhatsappTrait;

    protected $phone;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param string $phone
     * @param string $message
     * @return void
     */
    public function __construct($phone, $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Using the trait's method instead of direct HTTP call
        $response = $this->sendWhatsappMessage($this->phone, $this->message);
        Log::debug('Response from WhatsApp API:', ['response' => $response]);
    }
}
