<?php

namespace App\Jobs;

use App\Models\Penjualan;
use App\Models\WhatsappCrmProvider;
use App\Traits\CustomerServiceTrait;
use App\Traits\WhatsappTraitPing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoSendInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WhatsappTraitPing;

    protected $message;
    protected $phone;
    protected $apiKey;
    protected $deviceId;
    protected $penjualanId;
    protected $nextStep;

    /**
     * Create a new job instance.
     */
    public function __construct($message, $phone, $apiKey, $deviceId, $penjualanId, $nextStep)
    {
        $this->message  = $message;
        $this->phone    = $phone;
        $this->apiKey   = $apiKey;
        $this->deviceId = $deviceId;
        $this->penjualanId = $penjualanId;
        $this->nextStep = $nextStep;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $phone = $this->phone;
        $message = $this->message;
        $response = $this->sendWhatsappMessagePing($phone, $message, $this->apiKey, $this->deviceId);
        Penjualan::where('id', $this->penjualanId)->update(['is_sended' => $this->nextStep]);
    }
}
