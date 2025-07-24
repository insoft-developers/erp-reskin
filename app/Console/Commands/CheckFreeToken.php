<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckFreeToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-free-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $free_token_dailly = DB::table('ml_site_config')->first()->randuai_free_tokens_daily;
        DB::table('ml_accounts')->whereNotNull('id')->update(['randuai_tokens' => $free_token_dailly]);

        $this->info('done!');
    }
}
