<?php

namespace App\Jobs;

use App\Traits\WhatsappTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendWhatsAppMessageForgotPassword implements ShouldQueue
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
     */
    public function handle(): void
    {
        $name = $this->name;
        $id = SHA1($this->email);
        $link = $this->token;

        $message = "*Halo kak $name*, Istabel dari Randu lagi nih.\n";
        $message .= "Kami mengerti hal-hal seperti lupa password bisa terjadi. Tapi tidak perlu khawatir, kakak bisa mengatur ulang passwordmu dengan mudah.\n\n";
        $message .= "Untuk mengatur ulang password akun kakak, silakan klik tautan berikut (atau salin dan tempel ke browser):\n";
        $message .= url('forgot_password/reset_password') . "?id=$id&code=$link \n\n";
        $message .= "Setelah mengatur ulang, kakak bisa login dengan email dan password baru melalui tautan berikut ini:\n";
        $message .= url('/frontend_login') . "\n\n";
        $message .= "Jika kakak mengalami kesulitan, hubungi trainer melalui https://randu.co.id/chat/lewat-whatsapp/\n";
        $message .= "Salam hangat,\n*Istabel dari Randu*";

        // Using the trait's method instead of direct HTTP request
        $this->sendWhatsappMessage($this->phone, $message);
    }
}
