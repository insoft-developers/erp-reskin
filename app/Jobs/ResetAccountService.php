<?php

namespace App\Jobs;

use App\Traits\ResetAccountTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResetAccountService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ResetAccountTrait;

    protected $userid;
    /**
     * Create a new job instance.
     */
    public function __construct($userid)
    {
        $this->userid = $userid;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->resetUserData($this->userid);
    }
}
