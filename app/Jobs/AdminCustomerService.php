<?php

namespace App\Jobs;

use App\Traits\CustomerServiceTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminCustomerService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CustomerServiceTrait;

    protected $message;
    protected $phone;
    protected $appkey;
    protected $authkey;

    /**
     * Create a new job instance.
     */
    public function __construct($message, $phone, $appkey)
    {
        $this->message  = $message;
        $this->phone    = $phone;
        $this->appkey   = $appkey;
        $config         = DB::table('ml_site_config')->first();
        $this->authkey  = $config->imessage_auth_key;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Log::debug('cs admin rutinan ngirim ke ' . $this->phone);
        $this->sendMessage($this->message, $this->phone, $this->appkey, $this->authkey);
    }
}
