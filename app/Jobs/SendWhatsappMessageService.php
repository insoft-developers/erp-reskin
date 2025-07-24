<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappMessageService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $phone;
    protected $appKey;
    protected $authKey;

    /**
     * Create a new job instance.
     *
     * @param string $message
     * @param string $phone
     * @param string $appKey
     * @param string $authKey
     * @return void
     */
    public function __construct(string $message, string $phone, string $appKey, string $authKey)
    {
        $this->message = $message;
        $this->phone = $phone;
        $this->appKey = $appKey;
        $this->authKey = $authKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $debug = false;
        $data = [
            'appkey' => $this->appKey,
            'authkey' => $this->authKey,
            'to' => $this->phone,
            'message' => $this->message,
            'sandbox' => 'false'
        ];

        // Kirim permintaan HTTP menggunakan Laravel's Http facade
        $response = Http::asMultipart()->post('https://imessage.id/api/create-message', $data);

        if ($debug) {
            Log::debug('Sending WhatsApp message with data:', $data);

            // Log hasil dari permintaan HTTP
            if ($response->successful()) {
                Log::debug('WhatsApp message sent successfully', ['response' => $response->body()]);
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        }
    }
}
