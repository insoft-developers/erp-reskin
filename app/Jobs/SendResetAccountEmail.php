<?php

namespace App\Jobs;

use App\Mail\ResetAccountMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendResetAccountEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $name;
    protected $otp;

    /**
     * Create a new job instance.
     *
     * @param string $email
     * @param string $name
     * @param string $token
     * @return void
     */
    public function __construct($email, $name, $otp)
    {
        $this->email = $email;
        $this->name = $name;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $encryp_email = SHA1($this->email);
        $details = [
            'nama' => $this->name,
            'email' => $this->email,
            'otp' => $this->otp,
        ];

        // Mengirim email verifikasi menggunakan Mailable
        Mail::to($this->email)->send(new ResetAccountMail($details));
    }
}
