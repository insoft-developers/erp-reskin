<?php

namespace App\Jobs;

use App\Traits\WhatsappTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WhatsappTrait;

    protected $email;
    protected $phone;
    protected $name;
    protected $token;

    /**
     * Create a new job instance.
     *
     * @param string $email
     * @param string $phone
     * @param string $name
     * @param string $token
     * @return void
     */
    public function __construct($email, $phone, $name, $token)
    {
        $this->email = $email;
        $this->phone = $phone;
        $this->name = $name;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->name;
        $id = SHA1($this->email);
        $link = $this->token;
        $app_url = str_replace('https://', '', env('APP_URL'));

        $message = "*Halo kak $name*, Kenalin nama saya Istabel dari Randu\n";
        $message .= "Terima kasih telah mendaftar di Aplikasi Randu. Randu siap membantu memudahkan pengelolaan bisnis kakak secara digital.\n\n";
        $message .= "Untuk mengaktifkan akun, silakan klik tautan berikut (atau salin dan tempel ke browser):\n";
        $message .= url('account_activate') . "?id=$id&code=$link \n\n";
        $message .= "Setelah aktivasi, kak $name bisa login dengan email dan password yang telah kak $name daftarkan melalui tautan berikut ini:\n" . url('/frontend_login') . "\n\n";
        $message .= "Jika tautan tidak bisa diklik, simpan nomor saya terlebih dahulu ya, \n*Istabel dari Randu\n\n*";
        $message .= "Saya tidak bisa membalas Pesan dari kakak, jika ada pertanyaan kakak bisa bertanya Privat ke Trainer Randu. Silakan klik link berikut https://randu.co.id/chat/lewat-whatsapp/ ";

        // Using the trait's method instead of direct HTTP call
        $response = $this->sendWhatsappMessage($this->phone, $message);
        Log::debug('Response from WhatsApp API:', ['response' => $response]);
    }
}
