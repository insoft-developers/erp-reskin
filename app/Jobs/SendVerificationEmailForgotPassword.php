<?php

namespace App\Jobs;

use App\Mail\ForgotPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendVerificationEmailForgotPassword implements ShouldQueue
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
     */
    public function handle(): void
    {
        $details = [
            'nama' => $this->name,
            'email' => $this->email,
            'link' => $this->token,
            'id' => sha1($this->email)
        ];

        // Mengirim email verifikasi menggunakan Mailable
        Mail::to($this->email)->send(new ForgotPasswordMail($details));
    }
}
