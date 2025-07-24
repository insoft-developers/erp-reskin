<?php

namespace App\Jobs;

use App\Mail\RegisterMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $name;
    protected $token;

    /**
     * Create a new job instance.
     *
     * @param string $email
     * @param string $name
     * @param string $token
     * @return void
     */
    public function __construct($email, $name, $token)
    {
        $this->email = $email;
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
        $encryp_email = SHA1($this->email);
        $token = $this->token;
        $details = [
            'nama' => $this->name,
            'email' => $this->email,
            'link' => $this->token,
            'link_verification' => url('account_activate') . "?id=$encryp_email&code=$token",
            'id' => sha1($this->email)
        ];

        // Log::debug($details);

        // Mengirim email verifikasi menggunakan Mailable
        Mail::to($this->email)->send(new RegisterMail($details));
    }
}
