<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendMailKeting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $phone;
    protected $name;

    /**
     * Create a new job instance.
     *
     * @param string $email
     * @param string $phone
     * @param string $name
     * @param string $token
     * @return void
     */
    public function __construct($email, $phone, $name)
    {
        $this->email = $email;
        $this->phone = $phone;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $params = [
            'first_name'     => $this->name,
            'email'          => $this->email,
            'mobile'         => $this->phone,
            'api_token'      => '32eb25687322b584c9e87464dbef07fa',
            'list_id'        => '42898'
        ];

        Http::post('https://api.mailketing.co.id/api/v1/addsubtolist', $params);
    }
}
